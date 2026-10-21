<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvidenceController extends Controller
{
    public function download(Evidence $evidence)
    {
        $user = Auth::user();
        $transaction = $evidence->device()->with('transaction')->firstOrFail()->transaction;

        abort_unless($transaction && ($transaction->seller_id === $user->id || $transaction->buyer_id === $user->id), 403);

        return \Illuminate\Support\Facades\Storage::disk('public')->download($evidence->file_path, $evidence->file_name);
    }

    public function index()
    {
        $user = Auth::user();

        // Get evidence files linked to devices involved in user's transactions
        $evidenceItems = Evidence::query()
            ->whereHas('device.transaction', function ($q) use ($user) {
                $q->where('seller_id', $user->id)
                  ->orWhere('buyer_id', $user->id);
            })
            ->with(['device.transaction'])
            ->latest()
            ->paginate(12);

        return view('evidence.index', compact('evidenceItems'));
    }
}
