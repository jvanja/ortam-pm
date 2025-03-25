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
   * Show the form for creating a new resource.
   */
  public function create() {
    $this->authorize('client.create', Client::class);
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
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id) {
    $this->authorize('client.edit', Client::class);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id) {
    $this->authorize('client.edit', Client::class);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id) {
    $this->authorize('client.delete', Client::class);
  }
}
