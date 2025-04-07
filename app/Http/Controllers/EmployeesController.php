<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;


class EmployeesController extends Controller {
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request) {
    /** @var User $currentUser */
    $currentUser = Auth::user();

    // Ensure the user is authenticated and has an organization_id
    if (!$currentUser || !$currentUser->organization_id) {
      return Inertia::render('employees/Index', [
        'employees' => [],
      ]);
    }

    // Get all users that belong to the same organization as the current user
    $employees = User::where('organization_id', $currentUser->organization_id)->get();

    // Return the Inertia view with the employees data
    return Inertia::render('employees/Index', [
      'employees' => $employees,
    ]);
  }

  /**
   * Send an invitation to a new employee.
   */
  public function invite(Request $request): RedirectResponse {
    /** @var User $currentUser */
    $currentUser = Auth::user();

    if (!$currentUser || !$currentUser->organization_id) {
      // Should not happen if middleware protects this route, but good practice
      return back()->withErrors(['email' => 'Could not determine your organization.']);
    }

    $validated = $request->validate([
      'email' => [
        'required',
        'string',
        'email',
        'max:255',
        // Ensure the email doesn't already belong to an existing user
        Rule::unique('users', 'email'),
        Rule::unique('invitations', 'email')->where(function ($query) {
            return $query->where('expires_at', '>', now());
        }),
      ],
    ]);

    $emailToInvite = $validated['email'];
    $organizationId = $currentUser->organization_id;

    // --- TODO: Implement actual invitation logic ---
    // 1. Generate a unique, secure invitation token.
    // 2. Create an Invitation record (new Model/migration needed) storing:
    //    - email ($emailToInvite)
    //    - organization_id ($organizationId)
    //    - token
    //    - inviter_id ($currentUser->id)
    //    - expires_at (e.g., now()->addDays(7))
    // 3. Create a Mailable (e.g., EmployeeInvitationMail).
    // 4. Send the email using Mail::to($emailToInvite)->send(new EmployeeInvitationMail($invitation));
    // 5. Handle potential errors during DB save or email sending.
    // --- End TODO ---

    // For now, just log it and return success
    Log::info("Invitation requested for {$emailToInvite} to organization {$organizationId} by user {$currentUser->id}");

    // Use session flash message for Inertia
    return redirect()->back()->with('success', 'Invitation sent successfully!');
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
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id) {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id) {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id) {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id) {
    //
  }
}
