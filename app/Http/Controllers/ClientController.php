<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Inertia\Inertia;

class ClientController extends Controller {
  /**
   * Display a listing of the resource.
   */
  public function index() {

    $this->authorize('client.view', Client::class);

    $clients = Client::all();

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

    return Inertia::render('clients/Show', [
      'client' => $client,
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

    return redirect()->back()->with('success', 'Project updated successfully');
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
