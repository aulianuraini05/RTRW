<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    public function index()
    {
        $contributions = Contribution::all();

        return view('contributions.index', compact('contributions'));
    }

    public function create()
    {
        return view('contributions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'contribution_type' => 'required',
            'contribution_period' => 'required',
            'amount' => 'required|numeric',
            'payment_status' => 'required',
            'payment_date' => 'nullable|date',
        ]);

        Contribution::create($request->all());

        return redirect()->route('contributions.index');
    }

    public function show(Contribution $contribution)
    {
        return view('contributions.show', compact('contribution'));
    }

    public function edit(Contribution $contribution)
    {
        return view('contributions.edit', compact('contribution'));
    }

    public function update(Request $request, Contribution $contribution)
    {
        $request->validate([
            'contribution_type' => 'required',
            'contribution_period' => 'required',
            'amount' => 'required|numeric',
            'payment_status' => 'required',
            'payment_date' => 'nullable|date',
        ]);

        $contribution->update($request->all());

        return redirect()->route('contributions.index');
    }

    public function destroy(Contribution $contribution)
    {
        $contribution->delete();

        return redirect()->route('contributions.index');
    }
}