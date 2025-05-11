@component('mail::message')
# Invoice #{{ $invoice->invoice_number }}

Dear {{ $invoice->client->contact_person ?? $invoice->client->company_name }},

Please find attached Invoice #{{ $invoice->invoice_number }} for the amount of {{ number_format($invoice->amount, 2) }} {{ $invoice->project->currency }}.

This invoice is for the project: **{{ $invoice->project->type }}**.

You can view the invoice details online by clicking the button below:

@component('mail::button', ['url' => route('invoices.show', $invoice)])
View Invoice
@endcomponent

If you have any questions regarding this invoice, please do not hesitate to contact us.

Thank you for your business!

Regards,

{{ $invoice->organization->name ?? config('app.name') }}
@endcomponent
