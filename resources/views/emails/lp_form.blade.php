<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Details</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            color: #343a40;
            padding: 30px;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 30px;
            font-size: 16px;
        }
        .btn-complete {
            display: inline-block;
            padding: 12px 25px;
            font-size: 16px;
            color: white;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            text-align: center;
        }
        .btn-complete:hover {
            background-color: #218838;
        }
        .footer {
            text-align: center;
            padding: 15px;
            background-color: #f8f9fa;
            color: #6c757d;
            font-size: 14px;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            Complete Your Details
        </div>

        <div class="content">
            <p>Hello {{ $name }},</p>

            <p>We hope you are doing well! To proceed, please complete your details by clicking the button below:</p>
            
            <a href="{{ $link }}" class="btn-complete">Complete Your Details</a>

            <p>If you have any questions, feel free to reach out to us. We’re here to help!</p>

            <p>Thank you!</p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Your Company Name. All rights reserved.<br>
            <a href="https://yourcompanywebsite.com">www.yourcompanywebsite.com</a>
        </div>
    </div>
</body>
</html>
