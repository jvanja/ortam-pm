<?php

namespace Tests\Feature;

use App\Mail\InvoiceSent;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Elegantly\Invoices\Enums\InvoiceState;
use Elegantly\Invoices\Enums\InvoiceType;
use Elegantly\Invoices\Models\InvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase {
  use RefreshDatabase;

  protected User $user;
  protected Client $client;
  protected Project $project;
  protected Organization $organization;

  protected function setUp(): void {
    parent::setUp();

    // Create a user and authenticate
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->organization = Organization::factory()->create();
    // Create a client and project for testing
    $this->client = Client::factory()->create([
      'company_name' => 'Client Company',
      'address' => json_encode([
        'street' => '123 Client St',
        'city' => 'Clientville',
        'state' => 'Montana',
        'postal_code' => '93456',
        'country' => 'USA',
      ]),
      'email' => 'client@example.com',
      'organization_id' => 1,
    ]);
    $this->project = Project::factory()->create([
      'type' => 'project name',
      'currency' => 'USD',
      'organization_id' => 1,
      'client_id' => $this->client->id,
    ]);

    // Ensure default seller config exists for invoice creation
    config(['invoices.default_seller' => [
      'name' => 'Test Seller',
      'address' => ['street' => '456 Seller Ave', 'city' => 'Sellerton', 'postal_code' => 'SE', 'country' => 'USA'],
      'email' => 'seller@example.com',
    ]]);
  }

  /** @test */
  public function invoice_creation_page_can_be_rendered(): void {
    $response = $this->get(route('invoices.create'));

    $response->assertStatus(200);
    $response->assertInertia(
      fn($page) => $page
        ->component('invoices/Add')
        ->has('clients')
        ->has('projects')
        ->has('states')
    );
  }

  /** @test */
  public function invoice_can_be_created(): void {
    $invoiceData = [
      'type' => InvoiceType::Invoice->value, // Changed from InvoiceType::Invoice
      'state' => InvoiceState::Draft->value,
      'project_id' => $this->project->id,
      'client_id' => $this->client->id,
      'description' => 'Test Invoice Description',
      'total_amount' => 15000,
      'buyer_information' => $this->client->address,
      'items' => [
        [
          'label' => 'Item 1',
          'description' => 'Description for item 1',
          'quantity' => 2,
          'unit_price' => 5000,
        ],
        [
          'label' => 'Item 2',
          'description' => null, // Test nullable description
          'quantity' => 1,
          'unit_price' => 5000,
        ],
      ],
    ];

    $response = $this->post(route('invoices.store'), $invoiceData);

    // Assert the redirect happened first
    $response->assertRedirect();

    // Find the newly created invoice after the redirect
    // Using first() is okay here because RefreshDatabase ensures a clean state for each test
    $invoice = Invoice::first();
    $this->assertNotNull($invoice, 'Invoice was not created in the database.'); // Add a check just in case

    // Now assert the redirect URL using the found invoice
    $response->assertRedirect(route('invoices.show', $invoice));
    $response->assertSessionHas('success', 'Invoice created successfully!');

    $this->assertDatabaseCount('invoices', 1);

    $this->assertEquals($invoiceData['state'], $invoice->state);
    $this->assertEquals($invoiceData['project_id'], $invoice->project_id);
    $this->assertEquals($invoiceData['client_id'], $invoice->client_id);
    $this->assertEquals($invoiceData['description'], $invoice->description);
    // Note: total_amount is calculated on the frontend and sent, but the backend
    // should ideally calculate it from items for integrity. Testing the sent value for now.
    $this->assertEquals($invoiceData['total_amount'], $invoice->total_amount);
    $this->assertEquals($this->project->currency, $invoice->currency); // Check currency from project

    // Check buyer information structure
    $buyerInfo = $invoice->buyer_information;
    $this->assertEquals($this->client->company_name, $buyerInfo['name']);
    $this->assertEquals('123 Client St', $buyerInfo['address']['street']);
    $this->assertEquals('Clientville', $buyerInfo['address']['city']);
    $this->assertEquals('Montana', $buyerInfo['address']['state']);
    $this->assertEquals('93456', $buyerInfo['address']['postal_code']);
    $this->assertEquals('USA', $buyerInfo['address']['country']);
    $this->assertEquals($this->client->email, $buyerInfo['email']);

    // Check seller information
    $sellerInfo = $invoice->seller_information;
    $this->assertEquals(config('invoices.default_seller')['name'], $sellerInfo['name']);

    // Check invoice items
    $this->assertDatabaseCount('invoice_items', 2);
    $this->assertEquals(2, $invoice->items->count());

    $item1 = $invoice->items->firstWhere('label', 'Item 1');
    $this->assertNotNull($item1);
    $this->assertEquals('Description for item 1', $item1->description);
    $this->assertEquals(2, $item1->quantity);
    $this->assertEquals(50.00, $item1->unit_price);

    $item2 = $invoice->items->firstWhere('label', 'Item 2');
    $this->assertNotNull($item2);
    $this->assertNull($item2->description); // Check nullable description
    $this->assertEquals(1, $item2->quantity);
    $this->assertEquals(50.00, $item2->unit_price);
  }

  /** @test */
  public function invoice_creation_requires_valid_data(): void {
    $invoiceData = [
      // Missing state, project_id, client_id, items
      'description' => 'Invalid Invoice',
      'total_amount' => -100, // Invalid amount
      'items' => [
        [
          'label' => '', // Missing label
          'quantity' => 0, // Invalid quantity
          'unit_price' => -50, // Invalid price
        ],
      ],
    ];

    $response = $this->post(route('invoices.store'), $invoiceData);

    $response->assertSessionHasErrors(['state', 'project_id', 'client_id', 'total_amount', 'items.0.label', 'items.0.quantity', 'items.0.unit_price']);
    $this->assertDatabaseCount('invoices', 0);
    $this->assertDatabaseCount('invoice_items', 0);
  }

  /** @test */
  public function invoice_edit_page_can_be_rendered(): void {
    $invoice = Invoice::factory()->create([
      'client_id' => $this->client->id,
      'project_id' => $this->project->id,
    ]);
    InvoiceItem::factory()->count(2)->create(['invoice_id' => $invoice->id]);

    $response = $this->get(route('invoices.edit', $invoice));

    $response->assertStatus(200);
    $response->assertInertia(
      fn($page) => $page
        ->component('invoices/Edit')
        ->has('invoice', fn($prop) => $prop->where('id', $invoice->id)->etc())
        ->has('invoice_items')
        ->has('client')
        ->has('project')
        ->has('invoice_states')
    );
  }

  /** @test */
  public function invoice_can_be_updated(): void {
    $invoice = Invoice::factory()->create([
      'client_id' => $this->client->id,
      'project_id' => $this->project->id,
      'state' => InvoiceState::Draft->value,
      'total_amount' => 100.00,
    ]);
    $item1 = InvoiceItem::factory()->create(['invoice_id' => $invoice->id, 'label' => 'Old Item', 'quantity' => 1, 'unit_price' => 100]);
    $item2 = InvoiceItem::factory()->create(['invoice_id' => $invoice->id, 'label' => 'Another Old Item', 'quantity' => 1, 'unit_price' => 0]);


    $updatedData = [
      'state' => InvoiceState::Pending->value,
      'total_amount' => 250.00, // New total
      'items' => [
        [
          // Keep item1, update quantity and price
          'id' => $item1->id, // Include ID if frontend sends it, though backend deletes/recreates
          'label' => 'Updated Item 1',
          'description' => 'Updated description',
          'quantity' => 3,
          'unit_price' => 50.00,
        ],
        [
          // Add a new item
          'id' => null, // New item won't have an ID
          'label' => 'New Item',
          'description' => 'Description for new item',
          'quantity' => 1,
          'unit_price' => 100.00,
        ],
      ],
    ];

    $response = $this->put(route('invoices.update', $invoice), $updatedData);

    $response->assertRedirect(); // Controller redirects back
    $response->assertSessionHas('success', 'Invoice updated successfully!');

    $invoice->refresh(); // Refresh the model to get updated data

    $this->assertEquals($updatedData['state'], $invoice->state);
    $this->assertEquals($updatedData['total_amount'], $invoice->total_amount);
    // Check that client_id and project_id were NOT changed (as they are not in update validation/logic)
    $this->assertEquals($this->client->id, $invoice->client_id);
    $this->assertEquals($this->project->id, $invoice->project_id);


    // Check invoice items - backend deletes all and recreates
    $this->assertDatabaseCount('invoice_items', 2); // Should have 2 items now
    $this->assertEquals(2, $invoice->items->count());

    // Check the updated item
    $updatedItem1 = $invoice->items->firstWhere('label', 'Updated Item 1');
    $this->assertNotNull($updatedItem1);
    $this->assertEquals('Updated description', $updatedItem1->description);
    $this->assertEquals(3, $updatedItem1->quantity);
    $this->assertEquals(50.00, $updatedItem1->unit_price);

    // Check the new item
    $newItem = $invoice->items->firstWhere('label', 'New Item');
    $this->assertNotNull($newItem);
    $this->assertEquals('Description for new item', $newItem->description);
    $this->assertEquals(1, $newItem->quantity);
    $this->assertEquals(100.00, $newItem->unit_price);

    // Assert the old item that was not in the update data is deleted
    $this->assertDatabaseMissing('invoice_items', ['id' => $item2->id]);
  }

  /** @test */
  public function invoice_update_requires_valid_data(): void {
    $invoice = Invoice::factory()->create([
      'client_id' => $this->client->id,
      'project_id' => $this->project->id,
    ]);
    InvoiceItem::factory()->create(['invoice_id' => $invoice->id]);

    $updatedData = [
      'state' => 'invalid-state', // Invalid state
      'total_amount' => -50, // Invalid amount
      'items' => [
        [
          'label' => '', // Missing label
          'quantity' => 0, // Invalid quantity
          'unit_price' => -10, // Invalid price
        ],
      ],
    ];

    $response = $this->put(route('invoices.update', $invoice), $updatedData);

    $response->assertSessionHasErrors(['state', 'total_amount', 'items.0.label', 'items.0.quantity', 'items.0.unit_price']);
    // Assert invoice and items were not changed
    $invoice->refresh();
    $this->assertNotEquals('invalid-state', $invoice->state);
    $this->assertNotEquals(-50, $invoice->total_amount);
    $this->assertDatabaseCount('invoice_items', 1); // Items should not have been deleted/recreated
  }

  /** @test */
  public function invoice_can_be_deleted(): void {
    $invoice = Invoice::factory()->create([
      'client_id' => $this->client->id,
      'project_id' => $this->project->id,
    ]);
    InvoiceItem::factory()->count(3)->create(['invoice_id' => $invoice->id]);

    $this->assertDatabaseCount('invoices', 1);
    $this->assertDatabaseCount('invoice_items', 3);

    $response = $this->delete(route('invoices.destroy', $invoice));

    // Redirects to project show page with fragment
    $response->assertRedirect(route('projects.show', $this->project->id) . '#invoices');
    $response->assertSessionHas('success', 'Invoice deleted successfully!');

    $this->assertDatabaseCount('invoices', 0);
    $this->assertDatabaseCount('invoice_items', 0); // Items should be cascade deleted
  }

  /** @test */
  public function invoice_show_page_can_be_rendered(): void {
    $invoice = Invoice::factory()->create([
      'client_id' => $this->client->id,
      'project_id' => $this->project->id,
    ]);
    InvoiceItem::factory()->count(2)->create(['invoice_id' => $invoice->id]);

    // Assuming you have an InvoicePolicy that allows viewing
    // If not, you might need to temporarily disable policy checks for this test
    // or ensure the user has the necessary permissions.
    // For simplicity, this test assumes the policy allows the authenticated user to view.

    $response = $this->get(route('invoices.show', $invoice));

    $response->assertStatus(200);
    $response->assertInertia(
      fn($page) => $page
        ->component('invoices/Show')
        ->has('invoice', fn($prop) => $prop->where('id', $invoice->id)->etc())
        ->has('invoice_view') // Check if the HTML view is passed
    );

    // Check if relationships were loaded (optional, but good practice)
    $pageProps = $response->inertiaPage()['props'];
    $this->assertArrayHasKey('project', $pageProps['invoice']);
    $this->assertArrayHasKey('client', $pageProps['invoice']);
    $this->assertArrayHasKey('invoice_items', $pageProps['invoice']);
    $this->assertCount(2, $pageProps['invoice']['invoice_items']);
  }

  /** @test */
  public function invoice_can_be_sent_via_email(): void {
    // Ensure Mail fake is used
    \Mail::fake();

    $invoice = Invoice::factory()->create([
      'client_id' => $this->client->id,
      'project_id' => $this->project->id,
      'state' => InvoiceState::Draft->value, // Can only send draft/pending? Check policy/logic if needed
    ]);

    // Assuming you have an InvoicePolicy that allows editing (and thus sending)
    // For simplicity, this test assumes the policy allows the authenticated user.

    $response = $this->post(route('invoices.send', $invoice));

    $response->assertRedirect(); // Redirects back
    $response->assertSessionHas('success', 'Invoice sent successfully!');

    // Assert that the email was sent to the client's email address
    \Mail::assertSent(InvoiceSent::class, function ($mail) use ($invoice) {
      return $mail->hasTo($invoice->client->email);
    });

    // Optionally, assert the invoice state changed (if your logic does that)
    // $invoice->refresh();
    // $this->assertEquals(InvoiceState::Sent->value, $invoice->state);
  }

  /** @test */
  public function invoice_cannot_be_sent_if_client_has_no_email(): void {
    // Ensure Mail fake is used
    \Mail::fake();

    $clientWithoutEmail = Client::factory()->create(['email' => null]);
    $invoice = Invoice::factory()->create([
      'client_id' => $clientWithoutEmail->id,
      'project_id' => $this->project->id,
    ]);

    $response = $this->post(route('invoices.send', $invoice));

    $response->assertRedirect(); // Redirects back
    $response->assertSessionHas('error', 'Client does not have an email address.');

    // Assert that no email was sent
    \Mail::assertNothingSent();
  }
}
