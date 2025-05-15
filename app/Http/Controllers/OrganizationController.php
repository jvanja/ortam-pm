<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller {

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:organizations,name'],
      'address' => 'nullable|string|max:255',
      'logo' => 'nullable,mimes:jpg,jpeg,png|max:2048',
      'brand_color' => 'nullable|string|max:255',
    ]);

    // Create the organization
    $organization = Organization::create($validated);

    $request->user()->organization_id = $organization->id;
    $request->user()->save();

    // Redirect to the main dashboard after successful creation
    return redirect()->route('dashboard')->with('success', 'Organization created successfully.');
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Request $request): Response {
    $org_id = $request->user()->organization_id;
    $organization = Organization::find($org_id);
    return Inertia::render('settings/Organization', [
      'name' => $organization->name,
      'address' => $organization->address,
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function editBranding(Request $request): Response {
    $org_id = $request->user()->organization_id;
    $organization = Organization::find($org_id);
    return Inertia::render('settings/Branding', [
      'name' => $organization->name,
      'address' => $organization->address,
      // branding fields
      'logo' => $organization->logo,
      'brandColor' => $organization->brand_color,
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Organization $organization) {
    $validated = $request->validate([
      'name' => 'string|max:255|unique:organizations,name',
      'address' => 'nullable|string|max:255',
    ]);

    $organization->update($validated);
    return redirect()->back();
  }


  /**
   * Update the specified resource in storage.
   */
  public function updateBranding(Request $request, Organization $organization): Response {
    $validated = $request->validate([
      'logo' => 'mimes:jpg,jpeg,png|max:2048',
      'brand_color' => 'nullable|string|max:255',
    ]);
    // if ($request->hasFile('logo')) {
    //   $path = $request->file('logo')->store('uploads', 'public');
    // dd($path);
    // }

    dd($validated);
    $organization->update($validated);
    return Inertia::render('settings/Organization', []);
  }
  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Organization $organization) {
    //
  }
}
