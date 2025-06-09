<!DOCTYPE html>
<html>

<head>
  <title>Your Proposal</title>
</head>

<body>
  <h1>Hello {{ $managerName }},</h1>
  <p>Your proposal titled: <strong>{{ $proposalTitle }}</strong>.</p>
  <p>has been accepted by {{ $clientName }}.</p>
  <p><a href="{{ $proposalUrl }}">View Proposal</a></p>
</body>

</html>
