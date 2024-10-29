<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Retailer Information</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
            text-align: center; /* Center the button */
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
            margin-top: 2px;
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
            Complete Your Retailer Information
        </div>

        <div class="content">
            <p>Dear Retailer,</p>
            <p>We hope this message finds you well. To help us keep your information up to date, please take a moment to complete your retailer profile by clicking the button below:</p>
            
            <a href="{{ $link }}" class="btn-complete">Complete Your Details</a>

            <p class="mt-2">If you have any questions or require further assistance, feel free to contact us at any time.</p>
            <p>Thank you for your prompt attention to this matter.</p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Your Company Name. All rights reserved.<br>
            <a href="https://yourcompanywebsite.com">www.yourcompanywebsite.com</a>
        </div>
    </div>
</body>
</html>
