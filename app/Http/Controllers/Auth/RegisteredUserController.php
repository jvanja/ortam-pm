<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str; // Add Str facade for token generation if needed later, good practice to have

class RegisteredUserController extends Controller {
  /**
   * Show the registration page. Optionally pre-fill email if valid invitation token is provided.
   */
  public function create(Request $request): Response {
    $invitationEmail = null;
    $invitationToken = $request->query('token'); // Get token from query params

    if ($invitationToken) {
      $invitation = Invitation::where('token', $invitationToken)->first();
      // Ensure invitation exists and hasn't expired
      if ($invitation && !$invitation->hasExpired()) {
        $invitationEmail = $invitation->email;
      } else {
        // Optionally handle invalid/expired token view or redirect
        // For now, just nullify the token so the form doesn't use it
        $invitationToken = null;
      }
    }

    return Inertia::render('auth/Register', [
      'invitation_token' => $invitationToken,
      'email' => $invitationEmail, // Pass email to pre-fill the form
    ]);
  }

  /**
   * Handle an incoming registration request.
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  public function store(Request $request): RedirectResponse {
    $invitation = null;
    $invitationToken = $request->input('invitation_token'); // Get token from form input

    if ($invitationToken) {
      $invitation = Invitation::where('token', $invitationToken)->first();

      if (!$invitation || $invitation->hasExpired()) {
        // Throw validation exception if token is invalid or expired
        throw ValidationException::withMessages([
          'invitation_token' => __('The invitation link is invalid or has expired.'),
        ]);
      }

      // Validate request with invitation context
      $request->validate([
        'name' => 'required|string|max:255',
        // Ensure email matches the invitation and is lowercase
        'email' => [
          'required',
          'string',
          'lowercase',
          'email',
          'max:255',
          function ($attribute, $value, $fail) use ($invitation) {
            if (strtolower($value) !== strtolower($invitation->email)) {
              $fail(__('The email address does not match the invitation.'));
            }
          },
          // Check uniqueness against users table only if not invited (redundant here, but safe)
          // 'unique:'.User::class // We remove this because the user doesn't exist yet
        ],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'invitation_token' => 'required|string', // Ensure token is submitted
      ]);

      // Use invitation details
      $userData = [
        'name' => $request->name,
        'email' => $invitation->email, // Use email from invitation
        'password' => Hash::make($request->password),
        'email_verified_at' => date('Y-m-d G:i:s', time()),
        'organization_id' => $invitation->organization_id, // Use organization_id from invitation
      ];

    } else {
      // Original validation for non-invited registration
      $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
      ]);

      // Original user data creation
      $userData = [
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'organization_id' => null, // No organization for self-registration
      ];
    }

    // Use a transaction to ensure atomicity
    $user = DB::transaction(function () use ($userData, $invitation) {
        $newUser = User::create($userData);

        if ($invitation) {
            // Delete the invitation record after successful registration
            $invitation->delete();
        }

        return $newUser;
    });


    event(new Registered($user));

    Auth::login($user);

    return to_route('dashboard');
  }

  /**
   * Display the specified resource.
   */
  public function show($id) {
    $this->authorize('admin.view', User::class);
    $user = User::findOrFail($id); // Or handle not found

    return Inertia::render('users/Show', [
      'user' => $user,
    ]);
  }

  /**
   * Update user by {id}
   */
  public function update($user_id, Request $request) {
    $this->authorize('profile.edit', User::class);

    // validate email
    $validated = $request->validate([
     'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
    ]);

    $user = User::find($user_id);
    $user->update($validated);

    return redirect()->back()->with('success', 'User updated successfully');
  }

  /**
   * Switch user by {id} and store original user id in session
   */
  public function user_switch_start($new_user_id, Request $request) {
    $new_user_id = User::find($new_user_id);
    $request->session()->put('orig_user', Auth::id());
    Auth::login($new_user_id);
    return to_route('dashboard');
  }

  /**
   * Return to original user
   */
  public function user_switch_stop(Request $request) {
    $id = $request->session()->get('orig_user');
    $orig_user = User::find($id);
    Auth::login($orig_user);
    return to_route('dashboard');
  }
}
