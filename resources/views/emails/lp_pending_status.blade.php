{{-- resources/views/emails/lp_pending_status.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Status Pending</title>
</head>
<body>
    <p>Dear {{ $lpName }},</p>

    <p>Thank you for registering as a Supplier with us. Your registration status is currently <strong>pending</strong>.</p>
    
    <p>Our admin team will review your details, and you will be notified via email once your registration is either accepted or rejected.</p>

    <p>We appreciate your patience and look forward to working with you.</p>

    {{-- <p>If you have any questions, feel free to contact us at <a href="mailto:{{ $lpEmail }}">{{ $lpEmail }}</a>.</p> --}}

    <p>Best regards,<br>
    The Supplier Registration Team</p>
</body>
</html>
