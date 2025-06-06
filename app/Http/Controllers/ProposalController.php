<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceSent;
use App\Models\Client;
use App\Models\Project;
use App\Enums\ProposalState;
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
    $states = ProposalState::cases();

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
    $this->authorize('proposal.create', Proposal::class);

    $validated = $request->validate([
      'title' => ['required', 'string'],
      'description' => ['required', 'string'],
      'state' => ['required', 'string', Rule::in(array_column(ProposalState::cases(), 'value'))],
      'client_id' => ['required', 'exists:clients,id'],
      'project_id' => ['nullable', 'exists:projects,id'],
      'currency' => ['required', 'string'],
      'total_amount' => ['required', 'numeric', 'min:0'],
      'organization_id' => ['exists:organizations,id'],
    ]);
    $proposal = Project::create($validated);
    return redirect()->route('proposals.show', $proposal->id)
      ->with('message', 'Proposal created successfully');
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id) {
    $proposal = Proposal::with(['client', 'project'])->findOrFail($id);
    $proposal_view = $proposal->toPdfInvoice()->view()->toHtml();

    return Inertia::render('proposals/Show', [
      'proposal' => $proposal,
      'proposal_view' => $proposal_view,
    ]);

  }

  /**
   * Send the specified proposal via email.
   */
  public function send(Proposal $proposal): RedirectResponse {
    $this->authorize('proposal.edit', $proposal);

    $project = Project::find($proposal->project_id);
    $client = Client::find($proposal->client_id);

    $proposal->project = $project;
    $proposal->client = $client;

    // Check if the client has an email address
    if (empty($proposal->client->email)) {
      return redirect()->back()->with('error', 'Client does not have an email address.');
    }

    // Send the email
    // - TODO: remove my email below after testing
    Mail::to('jelicvanja@gmail.com')->send(new InvoiceSent($proposal));
    // Mail::to($proposal->client->email)->send(new InvoiceSent($proposal));

    return redirect()->back()->with('success', 'Proposal sent successfully!');
  }


  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Proposal $proposal) {
    $this->authorize('proposal.edit', Proposal::class);

    $client = Client::find($proposal->client_id);
    $project = Project::find($proposal->project_id);

    return Inertia::render('proposals/Edit', [
      'proposal' => $proposal,
      'client' => $client,
      'project' => $project,
    ]);
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
