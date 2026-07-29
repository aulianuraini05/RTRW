<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index()
    {
        $marketplaces = Marketplace::all();

        return view('marketplaces.index', compact('marketplaces'));
    }

    public function create()
    {
        return view('marketplaces.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'product_status' => 'required',
        ]);

        Marketplace::create($request->all());

        return redirect()->route('marketplaces.index');
    }

    public function show(Marketplace $marketplace)
    {
        return view('marketplaces.show', compact('marketplace'));
    }

    public function edit(Marketplace $marketplace)
    {
        return view('marketplaces.edit', compact('marketplace'));
    }

    public function update(Request $request, Marketplace $marketplace)
    {
        $request->validate([
            'product_name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'product_status' => 'required',
        ]);

        $marketplace->update($request->all());

        return redirect()->route('marketplaces.index');
    }

    public function destroy(Marketplace $marketplace)
    {
        $marketplace->delete();

        return redirect()->route('marketplaces.index');
    }
}