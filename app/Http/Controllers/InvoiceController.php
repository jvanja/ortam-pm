<?php

namespace App\Http\Controllers;

use Brick\Money\Money;
use Elegantly\Invoices\Enums\InvoiceState;
use App\Mail\InvoiceSent;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use Elegantly\Invoices\Enums\InvoiceType;
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
    $projects = Project::orderBy('type')->get(['id', 'type']);
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
      'state' => ['required', 'string', Rule::in(InvoiceState::cases())],
      'project_id' => ['required', 'exists:projects,id'],
      'client_id' => ['required', 'exists:clients,id'],
      'description' => ['string'],
    ]);
    \Validator::make($request->items, [
      'label' => 'required|string',
      'description' => 'string',
      'unit_price' => 'required|numeric',
      'quantity' => 'required|numeric',
    ]);

    $project = Project::find($validated['project_id']);
    $client = Client::find($validated['client_id']);

    $clientAddress = json_decode($client->address);
    $invoice = new Invoice([
      'type' => 'invoice',
      'state' => $validated['state'],
      'description' => $validated['description'],
      'total_amount' => $validated['total_amount'],
      'currency' => $project->currency,
      'seller_information' => config('invoices.default_seller'),
      'buyer_information' => [
        'name' => $client->company_name,
        'address' => [
          'street' => $clientAddress->street,
          'city' => $clientAddress->city,
          'postal_code' => $clientAddress->state,
          'country' => $clientAddress->country,
        ],
        'email' => $client->email,
        // 'tax_number' => "FR123456789",
      ],
    ]);
    $invoice->buyer()->associate($validated['client_id']); // optionnally associate the invoice to any model

    $invoice->save();

    foreach ($request->items as $item) {
      $invoiceItem = new InvoiceItem($item);
      $invoice->items()->save($invoiceItem);
    }
    // $invoice->items()->saveMany([
    //   new InvoiceItem([
    //     'unit_price' => Money::of(100, 'USD'),
    //     'unit_tax' => Money::of(20, 'USD'),
    //     'currency' => 'USD',
    //     'quantity' => 1,
    //     'label' => 'A label for my item',
    //     'description' => 'A description for my item',
    //   ]),
    // ]);

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
    $this->authorize('invoice.edit', Invoice::class);

    $validated = $request->validate([
      'total_amount' => ['required', 'numeric', 'min:0'],
      'state' => ['required', 'string', Rule::in(InvoiceState::cases())],
    ]);

    $invoice->update($validated);

    return redirect()->back()->with('success', 'Invoice updated successfully!');
  }


  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Invoice $invoice): RedirectResponse {
    $this->authorize('invoice.delete', Invoice::class);

    $invoice->delete();

    return redirect()->back()->with('success', 'Invoice deleted successfully!');
  }
}
