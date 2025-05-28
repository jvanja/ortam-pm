<?php

namespace App\Models;

use Elegantly\Invoices\Models\Invoice as BaseInvoice;
use App\Services\PdfInvoice;
use Elegantly\Invoices\Support\Buyer;
use Elegantly\Invoices\Support\PaymentInstruction;
use Elegantly\Invoices\Support\Seller;

class Invoice extends BaseInvoice {
  /**
   * Denormalize amounts computed from items to the invoice table
   * Allowing easier query
   */
  public function denormalize(): static {
    $pdfInvoice = $this->toPdfInvoice();
    $this->currency = $pdfInvoice->getCurrency();
    $this->subtotal_amount = $pdfInvoice->subTotalAmount();
    $this->discount_amount = $pdfInvoice->totalDiscountAmount();
    $this->tax_amount = $pdfInvoice->totalTaxAmount();
    $this->total_amount = $pdfInvoice->totalAmount();

    return $this;
  }

  /**
   * Override the toPdfInvoice method to customize the PDF generation.
   *
   * @return PdfInvoice
   */
  public function toPdfInvoice(): PdfInvoice {
    return new PdfInvoice(
      type: $this->getType(),
      state: $this->getState(),
      serial_number: $this->serial_number,
      due_at: $this->due_at,
      created_at: $this->created_at,
      buyer: Buyer::fromArray($this->buyer_information ?? []),
      seller: Seller::fromArray($this->seller_information ?? []),
      description: $this->description,
      items: $this->items->map(fn($item) => $item->toPdfInvoiceItem())->all(),
      tax_label: $this->getTaxLabel(),
      discounts: $this->getDiscounts(),
      logo: $this->getLogo(),
      paymentInstructions: [
        new PaymentInstruction(
          name: 'Bank Transfer',
          description: 'Make a direct bank transfer using the details below.',
          // qrcode: 'data:image/png;base64,' . base64_encode(
          //   file_get_contents(__DIR__ . '/../resources/images/qrcode.png')
          // ),
          fields: [
            'Bank Name' => 'Acme Bank',
            'Account Number' => '12345678',
            'IBAN' => 'GB12ACME12345678123456',
            'SWIFT/BIC' => 'ACMEGB2L',
            'Reference' => 'INV-0032/001',
            '<a href="#">Pay online</a>',
          ],
        ),
      ]
    );
  }
}
