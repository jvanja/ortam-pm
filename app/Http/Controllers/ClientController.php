<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Invoice;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller {
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request) {

    $this->authorize('client.view', Client::class);

    $searchQuery = $request->input('search');
    $clients = client::query()
      ->when($searchQuery, function (Builder $query, string $search) {
        // Search in 'company_name', 'contact_person'
        $query->where('company_name', 'like', "%{$search}%")
          ->orWhere('contact_person', 'like', "%{$search}%");
      })
      ->latest()
      ->paginate(10);

    return Inertia::render('clients/Index', [
      'clients' => [
        'data' => $clients->items(),
        'meta' => [
          'current_page' => $clients->currentPage(),
          'first_page_url' => $clients->url(1),
          'from' => $clients->firstItem(),
          'last_page' => $clients->lastPage(),
          'last_page_url' => $clients->url($clients->lastPage()),
          'links' => $clients->linkCollection(),
          'next_page_url' => $clients->nextPageUrl(),
          'path' => $clients->path(),
          'per_page' => $clients->perPage(),
          'prev_page_url' => $clients->previousPageUrl(),
          'to' => $clients->lastItem(),
          'total' => $clients->total(),
        ]
      ],
      'filters' => ['search' => $searchQuery]
    ]);
  }

  /**
   * Show the new project form
   */
  public function create() {
    return Inertia::render('clients/Add', []);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) {
    $this->authorize('client.create', Client::class);

    $validated = $request->validate([
      'company_name' => 'required|string|max:255',
      'contact_person' => 'nullable|string|max:255',
      'phone' => 'nullable|string|max:255',
      'email' => 'nullable|email|max:255',
      'address.street' => 'nullable|string|max:255',
      'address.city' => 'nullable|string|max:255',
      'address.state' => 'nullable|string|max:255',
      'address.postal_code' => 'nullable|string|max:20',
      'address.country' => 'nullable|string|max:255',
    ]);

    $backToProject = $request->input('backToProject');

    $client = Client::create([
      // ...$validated,
      'company_name' => $validated['company_name'],
      'contact_person' => $validated['contact_person'],
      'phone' => $validated['phone'],
      'email' => $validated['email'],
      'address' => json_encode($validated['address']),
      'organization_id' => Auth::user()->organization_id,
    ]);
    if ($backToProject) {

      return redirect()->route('projects.create', ['clientId' => $client->id])
        ->with('message', 'Client created successfully');
    } else {

      return redirect()->route('clients.create', $client->id)
        ->with('message', 'Client created successfully');
    }

  }

  /**
   * Display the specified resource.
   */
  public function show(string $id) {
    $this->authorize('client.view', Client::class);

    $client = Client::with(['projects', 'invoices'])->findOrFail($id);

    $projects = $client->projects->map(function ($project) {
      return ['id' => $project->id, 'name' => $project->type];
    });


    return Inertia::render('clients/Show', [
      'client' => $client,
      'invoices' => $client->invoices, // Use the eager-loaded invoices
      'projects' => $projects, // Pass the formatted projects list
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id) {
    $this->authorize('client.edit', Client::class);

    $client = Client::findOrFail($id);

    $validated = $request->validate([
      'company_name' => 'string',
      'contact_person' => 'sometimes|string',
      'address.street' => 'required|string|max:255',
      'address.city' => 'required|string|max:255',
      'address.state' => 'required|string|max:255',
      'address.postal_code' => 'required|string|max:20',
      'phone' => 'sometimes|string',
      'email' => 'required|email',
      'organization_id' => 'exists:organizations,id'
    ]);

    $client->update([
      'name' => $validated['name'],
      'company_name' => $validated['company_name'],
      'contact_person' => $validated['contact_person'],
      'address' => json_encode($validated['address']),
      'phone' => $validated['phone'],
      'email' => $validated['email'],
      'organization_id' => $validated['organization_id']
    ]);

    return redirect()->back()->with('success', 'Client updated successfully');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id) {
    $this->authorize('client.delete', Client::class);
    $client = Client::findOrFail($id);
    $client->delete();

    return redirect()->back()->with('success', 'Clent deleted successfully');
  }
}
