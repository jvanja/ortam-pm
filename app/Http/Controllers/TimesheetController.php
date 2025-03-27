<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timesheet;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TimesheetController extends Controller {
  /**
   * Display a listing of the resource.
   */
  public function index() {
    $this->authorize('timesheet.view', Timesheet::class);

    $user = Auth::user();

    $timesheets = $user->timesheets()->latest()->limit(5)->get();

    return Inertia::render('timesheets/Index', [
      'timesheets' => $timesheets,
    ]);
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
