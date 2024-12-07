{{-- resources/views/emails/lp_pending_status.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Registration Received</title>
</head>
<body>
    <p>Dear {{ $lpName }},</p>

    <p>Thank you for registering as a supplier with us. Your registration status is currently <strong>under review.</strong></p>
    
    <p>Our administrative team will carefully assess your details, and we will notify you via email once your registration has been either approved or declined.</p>

    <p>We appreciate your patience during this process and look forward to the opportunity to collaborate with you.</p>

    <p>If you have any questions or require assistance, please don’t hesitate to contact us at <strong>superadmin@gmail.com</strong></p>

    <p>Best regards,<br>
    The Supplier Registration Team</p>
</body>
</html>
