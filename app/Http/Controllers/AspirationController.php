<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use Illuminate\Http\Request;

class AspirationController extends Controller
{
    public function index()
    {
        $aspirations = Aspiration::all();

        return view('aspirations.index', compact('aspirations'));
    }

    public function create()
    {
        return view('aspirations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'aspiration_title' => 'required',
            'aspiration_content' => 'required',
            'category' => 'required',
            'submission_date' => 'required|date',
            'aspiration_status' => 'required',
        ]);

        Aspiration::create($request->all());

        return redirect()->route('aspirations.index');
    }

    public function show(Aspiration $aspiration)
    {
        return view('aspirations.show', compact('aspiration'));
    }

    public function edit(Aspiration $aspiration)
    {
        return view('aspirations.edit', compact('aspiration'));
    }

    public function update(Request $request, Aspiration $aspiration)
    {
        $request->validate([
            'aspiration_title' => 'required',
            'aspiration_content' => 'required',
            'category' => 'required',
            'submission_date' => 'required|date',
            'aspiration_status' => 'required',
        ]);

        $aspiration->update($request->all());

        return redirect()->route('aspirations.index');
    }

    public function destroy(Aspiration $aspiration)
    {
        $aspiration->delete();

        return redirect()->route('aspirations.index');
    }
}