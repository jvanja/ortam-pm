<!DOCTYPE html>
<html>

<head>
  <title>Your Proposal</title>
</head>

<body>
  <h1>Hello {{ $clientName }},</h1>
  <p>We are pleased to send you our proposal titled: <strong>{{ $proposalTitle }}</strong>.</p>
  <p>You can view the proposal by clicking on the link below:</p>
  <p><a href="{{ $proposalUrl }}">View Proposal</a></p>
  <p>If you have any questions, please do not hesitate to contact us.</p>
  <p>Thank you,<br>{{ config('app.name') }} Team</p>
</body>

</html>
