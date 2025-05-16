<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus; // Import the InvoiceStatus enum
use App\Mail\InvoiceSent; // Import the new Mailable
use App\Models\Client; // Import the Client model
use App\Models\Project; // Import the Project model
use Elegantly\Invoices\Models\Invoice;
use Elegantly\Invoices\Models\InvoiceItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail; // Import the Mail facade
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class InvoiceController extends Controller {
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
    // Fetch clients and projects for the dropdowns
    // Assuming Client and Project models have the organization global scope
    $clients = Client::orderBy('company_name')->get(['id', 'company_name']);
    $projects = Project::orderBy('type')->get(['id', 'type']);

    // Get the available invoice statuses from the enum
    $statuses = InvoiceStatus::cases();
    return Inertia::render('invoices/Add', [
        'clients' => $clients,
        'projects' => $projects,
        'statuses' => $statuses, // Pass statuses to the frontend
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) {
    // Validate the incoming request data
    $validated = $request->validate([
        'amount' => ['required', 'numeric', 'min:0'],
        'status' => ['required', 'string', Rule::in(InvoiceStatus::cases())], // Validate against enum values
        'project_id' => ['required', 'exists:projects,id'], // Ensure project exists
        'client_id' => ['required', 'exists:clients,id'], // Ensure client exists
        // invoice_number can be generated in the backend if needed, or added here if manual
    ]);

    // Create the new invoice
    // The organization_id will be automatically added by the global scope on the Invoice model
    $invoice = Invoice::create($validated);

    // Redirect to the newly created invoice's show page
    return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully!');
  }

  /**
   * Display the specified resource.
   */
  public function show(Invoice $invoice) {
    $this->authorize('invoice.view', Invoice::class);

    $project = Project::find($invoice->project_id);
    $client = Client::find($invoice->client_id);
    $invoice_items = InvoiceItem::where('invoice_id', $invoice->id)->get();

    $invoice->project = $project;
    $invoice->client = $client;
    $invoice->invoice_items = $invoice_items;

    return Inertia::render('invoices/Show', [
      'invoice' => $invoice,
    ]);
  }

  /**
   * Send the specified invoice via email.
   */
  public function send(Invoice $invoice): RedirectResponse {
    // Authorize the action (you might need a specific 'invoice.send' policy)
    $this->authorize('invoice.edit', $invoice); // Using 'edit' as a placeholder, adjust if needed

    // Ensure client relationship is loaded for the email address
    $invoice->load('client', 'project');

    // Check if the client has an email address
    if (empty($invoice->client->email)) {
        // Use Inertia's flash messages or redirect with error
        return redirect()->back()->with('error', 'Client does not have an email address.');
    }

    // Send the email
    // - TODO:
    // remove my email below after testing
    Mail::to('jelicvanja@gmail.com')->send(new InvoiceSent($invoice));
    // Mail::to($invoice->client->email)->send(new InvoiceSent($invoice));

    // Update the invoice status to 'sent' if it's not already paid or cancelled
    if (!in_array($invoice->status, ['paid', 'cancelled'])) {
        $invoice->status = 'sent';
        $invoice->save();
    }

    // Use Inertia's flash messages or redirect with success
    return redirect()->back()->with('success', 'Invoice sent successfully!');
  }


  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Invoice $invoice) {
    $this->authorize('invoice.edit', Invoice::class);

    // Load necessary relationships if needed, e.g., client, project
    $invoice->load(['client', 'project']);

    return Inertia::render('invoices/Edit', [
      'invoice' => $invoice,
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Invoice $invoice): RedirectResponse {
    $this->authorize('invoice.edit', Invoice::class);

    $validated = $request->validate([
      'amount' => ['required', 'numeric', 'min:0'],
      // Ensure status is one of the allowed values
      'status' => ['required', 'string', Rule::in(['draft', 'sent', 'paid', 'cancelled', 'overdue'])],
    ]);

    $invoice->update($validated);

    // Redirect back to the invoice show page with a success message
    // Using redirect()->back() might be better if the user could edit from other pages
    // return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully!');
    return redirect()->back()->with('success', 'Invoice updated successfully!');
  }


  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Invoice $invoice): RedirectResponse {
    $this->authorize('invoice.delete', Invoice::class);

    $invoice->delete();

    // Redirect to the index page after deletion with a success message
    // Ensure you have an 'invoices.index' route defined
    return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully!');
  }
}
