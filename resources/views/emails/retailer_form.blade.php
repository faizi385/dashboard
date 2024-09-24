<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Retailer Information</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .content {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-complete {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
        }
        .btn-complete:hover {
            background-color: #218838;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Complete Your Retailer Information</h1>
    </div>

    <div class="content">
        <p>Dear Retailer,</p>
        <p>Please click the button below to complete your retailer information:</p>
        
        <a href="{{ $link }}" class="btn-complete">Complete Retailer Form</a>

        <p>Thank you!</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
    </div>
</body>
</html>
