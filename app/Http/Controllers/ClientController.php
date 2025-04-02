<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Invoice;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;

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
        ->take(10) // Consider pagination for larger datasets
        ->get();
    return Inertia::render('clients/Index', [
      'clients' => $clients,
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) {
    $this->authorize('client.create', Client::class);
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id) {
    $this->authorize('client.view', Client::class);
    $client = Client::find($id);
    $invoices = Invoice::where('client_id', $id)->get();

    return Inertia::render('clients/Show', [
      'client' => $client,
      'invoices' => $invoices,
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
      'address' => 'sometimes|string',
      'phone' => 'sometmees:string',
      'email' => 'sometimes|email',
      'organization_id' => 'nullable|exists:organizations,id'
    ]);

    $client->update($validated);

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
