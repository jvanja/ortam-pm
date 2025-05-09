<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
