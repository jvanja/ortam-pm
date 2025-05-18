<?php

namespace App\Models;

use Elegantly\Invoices\Models\Invoice as BaseInvoice;
//use Elegantly\Invoices\Pdf\PdfInvoice;
use App\Services\PdfInvoice;
use Elegantly\Invoices\Enums\InvoiceType;
use Elegantly\Invoices\Enums\InvoiceState;
use Elegantly\Invoices\Support\Buyer;
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
      description: $this->description . ' - Custom Addition', // Example customization
      items: $this->items->map(fn($item) => $item->toPdfInvoiceItem())->all(),
      tax_label: $this->getTaxLabel(),
      discounts: $this->getDiscounts(),
      logo: $this->getLogo() ?? 'path/to/custom/logo.png', // Example custom logo
    );
  }
}
