@component('mail::message')
Hello {{ $managerName }},

Your proposal **{{ $proposalTitle }}** has been accepted by {{ $clientName }}.

@component('mail::button', ['url' => $proposalUrl])
  View Proposal
@endcomponent

@endcomponent
