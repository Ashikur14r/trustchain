<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Device;
use App\Models\Accessory;
use App\Models\Evidence;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Transaction::query()
            ->where(function ($q) use ($user) {
                $q->where('seller_id', $user->id)
                  ->orWhere('buyer_id', $user->id);
            })
            ->with(['device', 'seller', 'buyer', 'certificate'])
            ->latest();

        // Filter by role
        if ($request->filled('role')) {
            if ($request->role === 'seller') {
                $query->where('seller_id', $user->id);
            } elseif ($request->role === 'buyer') {
                $query->where('buyer_id', $user->id);
            }
        }

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['pending', 'confirmed', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        // Search by code or device brand/model
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhereHas('device', function ($dq) use ($search) {
                      $dq->where('brand', 'like', "%{$search}%")
                         ->orWhere('model', 'like', "%{$search}%")
                         ->orWhere('serial_number', 'like', "%{$search}%")
                         ->orWhere('imei', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->paginate(10)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        // Suggest registered users (excluding current user)
        $potentialBuyers = User::where('id', '!=', Auth::id())->orderBy('name')->get();
        return view('transactions.create', compact('potentialBuyers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'buyer_email' => ['required', 'email', 'exists:users,email'],
            'device_type' => ['required', 'string', 'max:50'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'imei' => ['nullable', 'string', 'max:50'],
            'agreed_price' => ['required', 'numeric', 'min:0'],
            'condition_description' => ['required', 'string'],
            'seller_notes' => ['nullable', 'string'],
            // Accessories
            'accessories' => ['nullable', 'array'],
            'accessories.*.name' => ['nullable', 'string', 'max:150'],
            'accessories.*.condition' => ['nullable', 'string', 'max:50'],
            // Evidence
            'evidence_files' => ['nullable', 'array'],
            'evidence_files.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'evidence_types' => ['nullable', 'array'],
            'evidence_descriptions' => ['nullable', 'array'],
        ]);

        $seller = Auth::user();
        $buyer = User::where('email', $validated['buyer_email'])->firstOrFail();

        if ($buyer->id === $seller->id) {
            return back()->withErrors(['buyer_email' => 'You cannot initiate a transaction with yourself as the buyer.'])->withInput();
        }

        $transaction = DB::transaction(function () use ($validated, $seller, $buyer, $request) {
            // Generate unique human-readable transaction code
            $code = 'TC-' . date('Y') . '-' . strtoupper(Str::random(5));
            while (Transaction::where('transaction_code', $code)->exists()) {
                $code = 'TC-' . date('Y') . '-' . strtoupper(Str::random(5));
            }

            $transaction = Transaction::create([
                'transaction_code' => $code,
                'seller_id' => $seller->id,
                'buyer_id' => $buyer->id,
                'status' => 'pending',
                'seller_notes' => $validated['seller_notes'] ?? null,
            ]);

            $device = Device::create([
                'transaction_id' => $transaction->id,
                'device_type' => $validated['device_type'],
                'brand' => $validated['brand'],
                'model' => $validated['model'],
                'serial_number' => $validated['serial_number'] ?? null,
                'imei' => $validated['imei'] ?? null,
                'condition_description' => $validated['condition_description'],
                'agreed_price' => $validated['agreed_price'],
            ]);

            // Save accessories
            if (!empty($validated['accessories'])) {
                foreach ($validated['accessories'] as $acc) {
                    if (!empty($acc['name'])) {
                        Accessory::create([
                            'device_id' => $device->id,
                            'name' => trim($acc['name']),
                            'condition' => $acc['condition'] ?? 'Good',
                        ]);
                    }
                }
            }

            // Save uploaded evidence with SHA-256 calculation
            if ($request->hasFile('evidence_files')) {
                $files = $request->file('evidence_files');
                $types = $request->input('evidence_types', []);
                $descriptions = $request->input('evidence_descriptions', []);

                foreach ($files as $index => $file) {
                    if ($file->isValid()) {
                        // Compute cryptographic SHA-256 hash
                        $hash = hash_file('sha256', $file->getRealPath());

                        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs("evidence/{$transaction->transaction_code}", $filename, 'public');

                        Evidence::create([
                            'device_id' => $device->id,
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'file_type' => $file->getMimeType(),
                            'evidence_type' => $types[$index] ?? 'condition',
                            'description' => $descriptions[$index] ?? null,
                            'file_hash' => $hash,
                        ]);
                    }
                }
            }

            return $transaction;
        });

        return redirect()->route('transactions.show', $transaction)
            ->with('success', "Transaction {$transaction->transaction_code} has been created and is awaiting buyer confirmation.");
    }

    public function show(Transaction $transaction)
    {
        $user = Auth::user();

        // Enforce participant authorization
        if ($transaction->seller_id !== $user->id && $transaction->buyer_id !== $user->id) {
            abort(403, 'Unauthorized access to this transaction record.');
        }

        $transaction->load([
            'device.evidence',
            'device.accessories',
            'seller',
            'buyer',
            'certificate',
        ]);

        return view('transactions.show', compact('transaction'));
    }

    public function confirm(Request $request, Transaction $transaction)
    {
        $user = Auth::user();

        // Only the assigned buyer can confirm
        if ($transaction->buyer_id !== $user->id) {
            abort(403, 'Only the designated buyer can confirm this transaction.');
        }

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'This transaction is already ' . $transaction->status . '.');
        }

        $validated = $request->validate([
            'buyer_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($transaction, $validated) {
            $transaction->update([
                'status' => 'confirmed',
                'buyer_notes' => $validated['buyer_notes'] ?? 'Transaction verified and accepted by buyer.',
                'confirmed_at' => Carbon::now(),
            ]);

            // Auto-generate Digital Certificate
            $certNumber = 'TC-CERT-' . date('Y') . '-' . mt_rand(10000, 99999);
            while (Certificate::where('certificate_number', $certNumber)->exists()) {
                $certNumber = 'TC-CERT-' . date('Y') . '-' . mt_rand(10000, 99999);
            }

            $token = 'verify-' . Str::random(24);

            Certificate::create([
                'transaction_id' => $transaction->id,
                'certificate_number' => $certNumber,
                'verification_token' => $token,
                'issued_at' => Carbon::now(),
            ]);
        });

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transaction confirmed! The digital ownership certificate has been issued.');
    }

    public function cancel(Request $request, Transaction $transaction)
    {
        $user = Auth::user();

        // Either party can cancel if still pending
        if ($transaction->seller_id !== $user->id && $transaction->buyer_id !== $user->id) {
            abort(403, 'Unauthorized action on this transaction.');
        }

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be cancelled.');
        }

        $reason = $request->input('cancel_reason', 'Cancelled by ' . ($user->id === $transaction->seller_id ? 'seller' : 'buyer') . '.');

        $transaction->update([
            'status' => 'cancelled',
            'seller_notes' => ($transaction->seller_notes ? $transaction->seller_notes . "\n" : '') . "[Cancelled]: " . $reason,
        ]);

        return redirect()->route('transactions.show', $transaction)
            ->with('info', 'Transaction has been marked as cancelled.');
    }
}
