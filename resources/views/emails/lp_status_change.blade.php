<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <p>Dear {{ $lp->name }},</p>
    @if($status === 'approved')
        <p>Congratulations! We are pleased to inform you that your registration as a supplier with us has been approved.</p>

        <p>Your account is now active, and you can access our supplier portal using your registered credentials. We are excited to have you on board and look forward to building a successful partnership with you.</p>

        <p>If you have any questions or require assistance, please don’t hesitate to contact us at <strong>superadmin@gmail.com</strong></p>

        <p>Thank you for choosing to work with us.</p>

        <p>Best regards,<br>
        The Supplier Registration Team</p>
    @elseif($status === 'rejected')
        <p>Thank you for registering as a supplier with us. After careful review of your details, we regret to inform you that your registration has not been approved at this time. We encourage you to review our supplier requirements and consider reapplying in the future.</p>
 
        <p>If you have any questions or need clarification, feel free to contact us at <strong>superadmin@gmail.com</strong><br>
        <br>
        We appreciate your interest in partnering with us and wish you success in your endeavors.</p>
        
        <p>Best regards,<br>
        The Supplier Registration Team</p>
        @endif
</body>
</html>
