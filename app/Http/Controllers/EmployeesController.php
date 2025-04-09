<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Invitation;
use App\Mail\EmployeeInvitationMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Throwable; // Import Throwable for catching exceptions


class EmployeesController extends Controller {
  /**
   * Display a listing of the resource.
   */
  public function index() {
    $currentUser = Auth::user();

    // Ensure the user is authenticated and has an organization_id
    if (!$currentUser || !$currentUser->organization_id) {
      return Inertia::render('employees/Index', [
        'employees' => [],
      ]);
    }

    // Get all users that belong to the same organization as the current user, eager load roles
    $employees = User::where('organization_id', $currentUser->organization_id)
      ->with('roles')
      ->get()
      ->map(function ($user) {
        $user->role = $user->roles->first()?->name ?? 'employee';
        return $user;
      });

    $invitees = Invitation::where('organization_id', $currentUser->organization_id)->get();

    // Return the Inertia view with the employees data including the role
    return Inertia::render('employees/Index', [
      'employees' => $employees,
      'invitees' => $invitees,
    ]);
  }

  /**
   * Send an invitation to a new employee.
   */
  public function invite(Request $request): RedirectResponse {
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
    $inviterId = $currentUser->id;

    $token = Str::random(60);

    // Use a database transaction to ensure atomicity
    DB::beginTransaction();
    try {
      $invitation = Invitation::create([
        'email' => $emailToInvite,
        'organization_id' => $organizationId,
        'token' => $token,
        'inviter_id' => $inviterId,
        'expires_at' => now()->addDays(7),
      ]);

      // Send Email using the Mailable.
      try {
        Mail::to($emailToInvite)->send(new EmployeeInvitationMail($invitation));
      } catch (\Exception $e) {
        Log::error("Failed to send invitation email to {$emailToInvite}: " . $e->getMessage(), ['exception' => $e]);
        dd($e);
        // return back()->withErrors(['email' => 'Failed to send invitation. Please check the email address and try again later, or contact support if the problem persists.'])->withInput(); // Retain the user's input
      }

      // If email sending is successful, commit the transaction
      DB::commit();

      Log::info("Invitation sent to {$emailToInvite} for organization {$organizationId} by user {$inviterId}");
      // Use session flash message for Inertia success feedback
      return redirect()->back()->with('success', 'Invitation sent successfully!');
    } catch (Throwable $e) {
      DB::rollBack();
      Log::error("Failed to send invitation to {$emailToInvite} for organization {$organizationId}: " . $e->getMessage(), ['exception' => $e]);

      return back()->withErrors(['email' => 'Failed to send invitation. Please check the email address and try again later, or contact support if the problem persists.'])->withInput(); // Retain the user's input
    }
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
