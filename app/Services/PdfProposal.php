<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ProposalState;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Elegantly\Invoices\Concerns\FormatForPdf;
use Elegantly\Invoices\Support\Buyer;
use Elegantly\Invoices\Support\Seller;
use Illuminate\Http\Response;
use Illuminate\Mail\Attachment;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\HeaderUtils;

class PdfProposal {
  use FormatForPdf;

  public string $state;

  public function __construct(
    ProposalState|string $state = ProposalState::Draft,
    public ?string $id = null,

    // 'client_id',
    // 'project_id',
    // 'invoice_id',
    // 'organization_id',
    public Seller $seller = new Seller,
    public Buyer $buyer = new Buyer,

    public ?string $title = null,
    public ?string $description = null,
    public ?string $currency = null,
    public ?string $subtotal_amount = null,
    public ?string $tax_amount = null,
    public ?string $total_amount = null,

    public ?Carbon $sent_at = null,
    public ?Carbon $accepted_at = null,
    public ?Carbon $rejected_at = null,
    public ?Carbon $expires_at = null,
  ) {
    $this->state = $state instanceof ProposalState ? $state->getLabel() : $state;
  }

  public function getFilename(): string {
    return str($this->id)
      ->replace(['/', '\\'], '_')
      ->append('.pdf')
      ->value();
  }

  public function totalAmount(): Money {
    $total = Money::of(0, $this->subtotal_amount)->multipliedBy($this->tax_amount / 100, roundingMode: RoundingMode::HALF_EVEN)->getAmount();
    return Money::of($total, $this->currency);
  }

  /**
   * @param  array<string, mixed>  $options
   * @param  array{ size?: string, orientation?: string }  $paper
   */
  public function pdf(array $options = [], array $paper = []): Dompdf {

    $pdf = new Dompdf(array_merge(
      // @phpstan-ignore-next-line
      config('invoices.pdf.options') ?? config('invoices.pdf_options') ?? [],
      $options,
    ));

    $pdf->setPaper(
      // @phpstan-ignore-next-line
      $paper['size'] ?? config('invoices.pdf.paper.size') ?? config('invoices.pdf.paper.paper') ?? config('invoices.paper_options.paper') ?? 'a4',
      // @phpstan-ignore-next-line
      $paper['orientation'] ?? config('invoices.pdf.paper.orientation') ?? config('invoices.paper_options.orientation') ?? 'portrait'
    );

    $html = $this->view()->render();

    $pdf->loadHtml($html);

    return $pdf;
  }

  public function getPdfOutput(): ?string {
    $pdf = $this->pdf();

    $pdf->render();

    return $pdf->output();
  }

  public function stream(?string $filename = null): Response {
    $filename ??= $this->getFilename();

    $output = $this->getPdfOutput();

    return new Response($output, 200, [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => HeaderUtils::makeDisposition('inline', $filename, Str::ascii($filename)),
    ]);
  }

  public function download(?string $filename = null): Response {
    $filename ??= $this->getFilename();

    $output = $this->getPdfOutput();

    return new Response($output, 200, [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => HeaderUtils::makeDisposition('attachment', $filename, Str::ascii($filename)),
      'Content-Length' => mb_strlen($output ?? ''),
    ]);
  }

  public function toMailAttachment(?string $filename = null): Attachment {
    return Attachment::fromData(fn() => $this->getPdfOutput())
      ->as($filename ?? $this->getFilename())
      ->withMime('application/pdf');
  }

  public function view(): \Illuminate\Contracts\View\View {
    return view('emails.proposals.pdf.proposal', ['proposal' => $this]);
  }
}
