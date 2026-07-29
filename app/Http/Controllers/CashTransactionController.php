<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use Illuminate\Http\Request;

class CashTransactionController extends Controller
{
    public function index()
    {
        $cashTransactions = CashTransaction::all();

        return view('cash_transactions.index', compact('cashTransactions'));
    }

    public function create()
    {
        return view('cash_transactions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_type' => 'required',
            'description' => 'required',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
        ]);

        CashTransaction::create($request->all());

        return redirect()->route('cash_transactions.index');
    }

    public function show(CashTransaction $cashTransaction)
    {
        return view('cash_transactions.show', compact('cashTransaction'));
    }

    public function edit(CashTransaction $cashTransaction)
    {
        return view('cash_transactions.edit', compact('cashTransaction'));
    }

    public function update(Request $request, CashTransaction $cashTransaction)
    {
        $request->validate([
            'transaction_type' => 'required',
            'description' => 'required',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
        ]);

        $cashTransaction->update($request->all());

        return redirect()->route('cash_transactions.index');
    }

    public function destroy(CashTransaction $cashTransaction)
    {
        $cashTransaction->delete();

        return redirect()->route('cash_transactions.index');
    }
}