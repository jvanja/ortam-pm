<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller {
  /**
   * Show the registration page.
   */
  public function create(): Response {
    return Inertia::render('auth/Register');
  }

  /**
   * Handle an incoming registration request.
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  public function store(Request $request): RedirectResponse {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

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
    dd($request->session()->all());

    $user = User::find($user_id);
    return redirect()->back();
  }

  /**
   * Switch user by {id} and store original user id in session
   */
  public function user_switch_start($new_user_id, Request $request) {
    // dd($request->session()->all());
    $new_user_id = User::find($new_user_id);
    $request->session()->put('orig_user', Auth::id());
    Auth::login($new_user_id);
    return redirect()->back();
  }

  /**
   * Return to original user
   */
  public function user_switch_stop(Request $request) {
    $id = $request->session()->get('orig_user');
    $orig_user = User::find($id);
    Auth::login($orig_user);
    return redirect()->back();
  }
}
