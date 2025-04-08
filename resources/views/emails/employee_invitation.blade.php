<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation to Join {{ $invitation->organization->name }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 3px; }
        .footer { margin-top: 20px; font-size: 0.9em; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <h1>You're Invited!</h1>

        <p>Hello,</p>

        <p>
            You have been invited by <strong>{{ $invitation->inviter->name }}</strong> ({{ $invitation->inviter->email }})
            to join the organization <strong>{{ $invitation->organization->name }}</strong> on our platform.
        </p>

        <p>
            To accept this invitation and create your account, please click the button below:
        </p>

        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $registrationUrl }}" class="button" style="color: #ffffff;">Accept Invitation & Register</a>
        </p>

        <p>
            This invitation link is valid until {{ $invitation->expires_at->format('F j, Y, g:i a T') }}.
            If you click the link after it expires, you may need to request a new invitation.
        </p>

        <p>
            If you did not expect this invitation, you can safely ignore this email. Your email address will not be used further unless you accept the invitation.
        </p>

        <div class="footer">
            <p>Thanks,<br>The {{ config('app.name', 'Laravel') }} Team</p>
            <p><small>If you're having trouble clicking the "Accept Invitation & Register" button, copy and paste the URL below into your web browser:<br>{{ $registrationUrl }}</small></p>
        </div>
    </div>
</body>
</html>
