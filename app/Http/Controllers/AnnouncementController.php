<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::all();

        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'announcement_title' => 'required',
            'announcement_content' => 'required',
            'publication_date' => 'required|date',
        ]);

        Announcement::create($request->all());

        return redirect()->route('announcements.index');
    }

    public function show(Announcement $announcement)
    {
        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'announcement_title' => 'required',
            'announcement_content' => 'required',
            'publication_date' => 'required|date',
        ]);

        $announcement->update($request->all());

        return redirect()->route('announcements.index');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('announcements.index');
    }
}