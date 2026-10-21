<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $soldTransactions = $user->soldTransactions()
            ->with(['device', 'seller', 'buyer', 'certificate'])
            ->latest()
            ->get();

        $boughtTransactions = $user->boughtTransactions()
            ->with(['device', 'seller', 'buyer', 'certificate'])
            ->latest()
            ->get();

        $transactions = $soldTransactions
            ->merge($boughtTransactions)
            ->sortByDesc('created_at');

        $totalTransactions = $transactions->count();

        $pendingTransactions = $transactions
            ->where('status', 'pending')
            ->count();

        $confirmedTransactions = $transactions
            ->where('status', 'confirmed')
            ->count();

        $certificatesCount = $transactions
            ->filter(fn ($t) => $t->certificate !== null)
            ->count();

        $evidenceCount = $transactions
            ->filter(fn ($t) => $t->device !== null)
            ->sum(fn ($t) => $t->device->evidence()->count());

        $verifiedTransactionsCount = $confirmedTransactions;

        $recentTransactions = $transactions
            ->take(5);

        return view('dashboard.index', compact(
            'totalTransactions',
            'pendingTransactions',
            'confirmedTransactions',
            'certificatesCount',
            'evidenceCount',
            'verifiedTransactionsCount',
            'recentTransactions'
        ));
    }
}