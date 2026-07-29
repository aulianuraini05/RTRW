<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\Request;

class LetterController extends Controller
{
    public function index()
    {
        $letters = Letter::all();

        return view('letters.index', compact('letters'));
    }

    public function create()
    {
        return view('letters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'letter_number' => 'required|unique:letters,letter_number',
            'letter_type' => 'required',
            'submission_date' => 'required|date',
            'letter_date' => 'nullable|date',
            'purpose' => 'nullable',
            'letter_status' => 'required',
        ]);

        Letter::create($request->all());

        return redirect()->route('letters.index');
    }

    public function show(Letter $letter)
    {
        return view('letters.show', compact('letter'));
    }

    public function edit(Letter $letter)
    {
        return view('letters.edit', compact('letter'));
    }

    public function update(Request $request, Letter $letter)
    {
        $request->validate([
            'letter_number' => 'required|unique:letters,letter_number,' . $letter->id,
            'letter_type' => 'required',
            'submission_date' => 'required|date',
            'letter_date' => 'nullable|date',
            'purpose' => 'nullable',
            'letter_status' => 'required',
        ]);

        $letter->update($request->all());

        return redirect()->route('letters.index');
    }

    public function destroy(Letter $letter)
    {
        $letter->delete();

        return redirect()->route('letters.index');
    }
}