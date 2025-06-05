<?php

namespace App\Http\Controllers;

use Elegantly\Invoices\Enums\InvoiceState;
use App\Mail\InvoiceSent;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use Elegantly\Invoices\Models\InvoiceItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class InvoiceController extends Controller {

  /**
   * Show the form for creating a new resource.
   */
  public function create() {
    $clients = Client::orderBy('company_name')->get(['id', 'company_name']);
    $projects = Project::orderBy('type')->get(['id', 'type', 'currency']);
    $states = InvoiceState::cases();

    return Inertia::render('invoices/Add', [
      'clients' => $clients,
      'projects' => $projects,
      'states' => $states,
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) {
    $validated = $request->validate([
      'total_amount' => ['required', 'numeric', 'min:0'],
      'state' => ['required', 'string', Rule::in(array_column(InvoiceState::cases(), 'value'))],
      'project_id' => ['required', 'exists:projects,id'],
      'client_id' => ['required', 'exists:clients,id'],
      'description' => ['nullable', 'string'],
      'items' => ['required', 'array', 'min:1'],
      'items.*.label' => ['required', 'string'],
      'items.*.description' => ['nullable', 'string'],
      'items.*.unit_price' => ['required', 'numeric', 'min:0'],
      'items.*.quantity' => ['required', 'numeric', 'min:1'],
    ]);

    $project = Project::find($validated['project_id']);
    $client = Client::find($validated['client_id']);

    // Decode client address safely
    $clientAddress = json_decode($client->address ?? '{}', true);

    $invoice = new Invoice([
      'type' => 'invoice',
      'state' => $validated['state'],
      'description' => $validated['description'] ?? null, // Use validated description, default to null
      'total_amount' => $validated['total_amount'],
      'currency' => $project->currency ?? 'USD',
      'seller_information' => config('invoices.default_seller'),
      'buyer_information' => [
        'name' => $client->company_name,
        'address' => [
          'street' => $clientAddress['street'] ?? null,
          'city' => $clientAddress['city'] ?? null,
          'postal_code' => $clientAddress['state'] ?? null, // Assuming state is postal_code based on your code
          'country' => $clientAddress['country'] ?? null,
        ],
        'email' => $client->email,
        // 'tax_number' => "FR123456789", // Example, keep commented or remove if not used
      ],
    ]);
    $invoice->buyer()->associate($validated['client_id']); // optionnally associate the invoice to any model

    $invoice->save();

    foreach ($validated['items'] as $itemData) {
      $invoiceItem = new InvoiceItem([
          'label' => $itemData['label'],
          'description' => $itemData['description'] ?? null,
          'quantity' => $itemData['quantity'],
          'unit_price' => $itemData['unit_price'],
      ]);
      $invoice->items()->save($invoiceItem);
    }
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

    /** @disregard [OPTIONAL_CODE] [OPTION_DESCRIPTION] */
    $invoice_view = $invoice->toPdfInvoice()->view()->toHtml();
    return Inertia::render('invoices/Show', [
      'invoice' => $invoice,
      'invoice_view' => $invoice_view,
    ]);
  }

  /**
   * Send the specified invoice via email.
   */
  public function send(Invoice $invoice): RedirectResponse {
    $this->authorize('invoice.edit', $invoice);

    $project = Project::find($invoice->project_id);
    $client = Client::find($invoice->client_id);

    $invoice->project = $project;
    $invoice->client = $client;

    // Check if the client has an email address
    if (empty($invoice->client->email)) {
      return redirect()->back()->with('error', 'Client does not have an email address.');
    }

    // Send the email
    // - TODO: remove my email below after testing
    Mail::to('jelicvanja@gmail.com')->send(new InvoiceSent($invoice));
    // Mail::to($invoice->client->email)->send(new InvoiceSent($invoice));

    return redirect()->back()->with('success', 'Invoice sent successfully!');
  }


  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Invoice $invoice) {
    $this->authorize('invoice.edit', Invoice::class);

    $client = Client::find($invoice->client_id);
    $project = Project::find($invoice->project_id);
    $invoice_items = InvoiceItem::where('invoice_id', $invoice->id)->select(['id', 'label', 'quantity', 'unit_price', 'description'])->get();
    $states = InvoiceState::cases();

    return Inertia::render('invoices/Edit', [
      'invoice' => $invoice,
      'invoice_items' => $invoice_items,
      'client' => $client,
      'project' => $project,
      'invoice_states' => $states,
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Invoice $invoice): RedirectResponse {
    $this->authorize('invoice.edit', $invoice);

    $validated = $request->validate([
      'total_amount' => ['required', 'numeric', 'min:0'],
      'description' => ['nullable', 'string'],
      'state' => ['required', 'string', Rule::in(array_column(InvoiceState::cases(), 'value'))],
      'items' => ['required', 'array', 'min:1'],
      'items.*.id' => ['nullable', 'exists:invoice_items,id'],
      'items.*.label' => ['required', 'string'],
      'items.*.description' => ['nullable', 'string'],
      'items.*.unit_price' => ['required', 'numeric', 'min:0'],
      'items.*.quantity' => ['required', 'numeric', 'min:1'],
    ]);

    $invoice->update([
        'total_amount' => $validated['total_amount'],
        'state' => $validated['state'],
        'description' => $validated['description'] ?? null,
    ]);

    $invoice->items()->delete();

    foreach ($validated['items'] as $itemData) { // Use validated items
      $invoiceItem = new InvoiceItem([
          'label' => $itemData['label'],
          'description' => $itemData['description'] ?? null,
          'quantity' => $itemData['quantity'],
          'unit_price' => $itemData['unit_price'],
      ]);
      $invoice->items()->save($invoiceItem);
    }

    return redirect()->back()->with('success', 'Invoice updated successfully!');
  }


  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Invoice $invoice): RedirectResponse {
    $this->authorize('invoice.delete', Invoice::class);

    $project_id = $invoice->project_id;
    $invoice->delete();

    return redirect(route('projects.show', [$project_id]))->withFragment('#invoices')->with('success', 'Invoice deleted successfully!');
  }
}
