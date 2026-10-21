<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $certificates = Certificate::query()
            ->whereHas('transaction', function ($q) use ($user) {
                $q->where('seller_id', $user->id)
                  ->orWhere('buyer_id', $user->id);
            })
            ->with(['transaction.device', 'transaction.seller', 'transaction.buyer'])
            ->latest('issued_at')
            ->paginate(10);

        return view('certificates.index', compact('certificates'));
    }

    public function show(Certificate $certificate)
    {
        $user = Auth::user();

        // Enforce participation authorization for internal show view
        if ($certificate->transaction->seller_id !== $user->id && $certificate->transaction->buyer_id !== $user->id) {
            abort(403, 'Unauthorized access to this certificate.');
        }

        $certificate->load([
            'transaction.device.evidence',
            'transaction.device.accessories',
            'transaction.seller',
            'transaction.buyer',
        ]);

        return view('certificates.show', compact('certificate'));
    }

    public function verifyForm(Request $request)
    {
        if ($request->filled('query')) {
            $cleaned = trim($request->input('query'));
            return redirect()->route('certificates.verify.show', $cleaned);
        }

        return view('certificates.verify');
    }

    public function verifyShow($token)
    {
        $certificate = Certificate::query()
            ->where('verification_token', $token)
            ->orWhere('certificate_number', $token)
            ->with([
                'transaction.device.evidence',
                'transaction.device.accessories',
                'transaction.seller',
                'transaction.buyer',
            ])
            ->first();

        return view('certificates.verify_result', compact('certificate', 'token'));
    }
}
