<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller {
  /**
   * Display a listing of the resource.
   */
  public function index() {
    //
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create() {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:organizations,name'], // Added unique constraint
    ]);

    // Create the organization
    $organization = Organization::create($validated);

    $request->user()->organization_id = $organization->id;
    $request->user()->save();

    // Redirect to the main dashboard after successful creation
    return redirect()->route('dashboard')->with('success', 'Organization created successfully.');
  }

  /**
   * Display the specified resource.
   */
  public function show(Organization $organization) {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Request $request): Response {
    $org_id = $request->user()->organization_id;
    $organization = Organization::find($org_id);
    return Inertia::render('settings/Company', [
      'name' => $organization->name,
      'address' => $organization->address,
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Organization $organization) {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Organization $organization) {
    //
  }
}
