<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller {

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request): RedirectResponse {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:organizations,name'],
      'email' => ['required', 'string', 'email', 'unique:organizations,email'],
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
      'email' => $organization->email,
      'phone' => $organization->phone,
      'payment_instructions' => $organization->payment_instructions,
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
      'logo' => $organization->logo,
      'brand_color' => $organization->brand_color,
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Organization $organization): RedirectResponse {
    $validated = $request->validate([
      'name' => 'string|max:255',
      'email' => 'required|string|max:255|email',
      'phone' => 'required|string|max:255',
      'address.street' => 'required|string|max:255',
      'address.city' => 'required|string|max:255',
      'address.state' => 'required|string|max:255',
      'address.postal_code' => 'required|string|max:20',
      'address.country' => 'required|string|max:255',
      'payment_instructions' => 'nullable|string|max:255',
    ]);

    $organization->update([
      'name' => $validated['name'],
      'email' => $validated['email'],
      'phone' => $validated['phone'],
      'address' => json_encode($validated['address']),
      'payment_instructions' => $validated['payment_instructions'],
    ]);

    return redirect()->back()->with('status', 'Organization updated!');
  }


  /**
   * Update the specified resource in storage.
   */
  public function updateBranding(Request $request, Organization $organization): RedirectResponse {
    $validated = $request->validate([
      'logo' => 'nullable|mimes:jpg,jpeg,png|max:2048',
      'brand_color' => 'nullable|string|max:255',
    ]);


    if ($request->hasFile('logo')) {
      $path = $request->file('logo')->store('logos', 'public'); // Store in 'storage/app/public/logos'
      if ($path) {
        $imageUrl = Storage::url($path); // Generate the public URL
        $organization->logo = $imageUrl;
      } else {
        // Handle error: file not stored
        return redirect()->back()->with('error', 'Failed to upload logo.');
      }
    }

    // Example: Update brand color
    if (isset($validated['brand_color'])) {
      $organization->brand_color = $validated['brand_color'];
    }

    $organization->save();

    // Redirect or return a response
    return redirect()->back()->with('success', 'Branding updated successfully.');
  }
  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Organization $organization) {
    //
  }
}
