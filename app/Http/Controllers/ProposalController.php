<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceSent;
use App\Models\Client;
use App\Models\Project;
use App\Enums\ProposalStatus;
use App\Models\Proposal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProposalController extends Controller {

  /**
   * Display a listing of the resource.
   */
  public function index() {
    $proposals = Proposal::with(['client', 'project'])->get();

    return Inertia::render('proposals/Index', [
      'proposals' => $proposals,
    ]);
  }
  /**
   * Show the form for creating a new resource.
   */
  public function create() {
    $clients = Client::orderBy('company_name')->get(['id', 'company_name']);
    $projects = Project::orderBy('type')->get(['id', 'type']);
    $states = ProposalStatus::cases();

    return Inertia::render('proposals/Add', [
      'clients' => $clients,
      'projects' => $projects,
      'states' => $states,
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) {
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id) {
    $proposal = Proposal::with(['client', 'project'])->findOrFail($id);

    return Inertia::render('proposals/Index', [
      'proposal' => $proposal,
    ]);

  }

  /**
   * Send the specified invoice via email.
   */
  public function send(Proposal $proposal): RedirectResponse {

    return redirect()->back()->with('success', 'Invoice sent successfully!');
  }


  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Proposal $proposal) {
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Proposal $proposal): RedirectResponse {
  }


  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Proposal $proposal): RedirectResponse {
  }
}
