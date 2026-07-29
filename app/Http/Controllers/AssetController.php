<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index ()
    {
        $assets = Aseet::all();
        
        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
        'assets_name' => 'required',
        'assets_type' => 'required',
        'quantity' => 'required | integer',
        'condition' => 'required',
        'description' => 'nullable',
    ]);
      Assets::create($request->all());
    
      return redirect()->route('assets.index');
    }

    public function show (Asset $asset)
    {
        return view('assets.edit', compact('asset'));
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'asset_name'=> 'required',
            'asset_type'=>'required',
            'quantity'=>'required|integer',
            'condition'=>'required',
            'description'=>'nullable',
        ]);

        $asset->update($request->all());
        return redirect()->route('assets.index');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index');
    }
}
