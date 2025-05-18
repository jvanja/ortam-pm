<?php

namespace App\Services;

use Elegantly\Invoices\Pdf\PdfInvoice as BasePdfInvoice;

class PdfInvoice extends BasePdfInvoice {
  /**
   * Override the method that renders the view to use the custom template path.
   */
  public function view(): \Illuminate\Contracts\View\View {
    // Use the custom view path without the 'invoices::' prefix
    return view('emails.invoices.pdf.invoice', ['invoice' => $this]);
  }
}
