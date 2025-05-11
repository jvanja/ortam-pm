<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceSent; // Import the new Mailable
use App\Models\Invoice;
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
  public function show(Invoice $invoice) {
    $this->authorize('invoice.view', Invoice::class);

    // Load necessary relationships
    $invoice->load(['client', 'project', 'organization']);

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
