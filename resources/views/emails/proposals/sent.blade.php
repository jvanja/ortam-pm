@component('mail::message')
Hello {{ $clientName }},

We are pleased to send you our proposal titled: **{{ $proposalTitle }}**.

You can view the proposal by clicking on the link below:
@component('mail::button', ['url' => $proposalUrl])
  View Proposal
@endcomponent

If you have any questions, please do not hesitate to contact us.

Thank you,

{{ config('app.name') }} Team
@endcomponent
