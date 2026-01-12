<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Inertia\Inertia;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        // Fetch all leads from the database
        // We use latest() to show the most recent leads first
        $leads = Lead::latest()->get();

        return Inertia::render('Leads', [
            'leads' => $leads
        ]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:leads,email',
        'status' => 'required|string',
    ]);

    Lead::create($validated);

    return redirect()->route('leads.index')->with('success', 'Lead created successfully!');
}

public function update(Request $request, Lead $lead)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:leads,email,' . $lead->id,
        'status' => 'required|string',
    ]);

    $lead->update($validated);

    return back()->with('success', 'Lead updated successfully!');
}
}