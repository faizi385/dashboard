<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LP Status Update</title>
</head>
<body>
    <h1>LP Status Update</h1>
    <p>Dear {{ $lp->name }},</p>
    <p>Your LP has been <strong>{{ $status }}</strong>.</p>

    @if($status === 'approved')
        <p>Congratulations! Your LP has been approved. You can now proceed with further actions.</p>
        <p>
            <a href="{{ route('login') }}" style="display: inline-block; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">
                Access Your Dashboard
            </a>
        </p>
    @elseif($status === 'rejected')
        <p>We regret to inform you that your LP has been rejected. Please review the requirements and try again.</p>
    @endif

    <p>Thank you!</p>
</body>
</html>
