<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Created</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .message-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: slideIn 1s ease-out;
        }
        .btn-primary {
            background-color: #171718;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        @keyframes slideIn {
            from {
                transform: translateY(50%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="message-container">
        <h1>Account Created!</h1>
        <p>Your account has been created. You will be informed via email once the <br> super admin approves or rejects your registration.</p>
        <a href="{{ route('login') }}" class="btn btn-primary mt-3">Go to Login</a>
    </div>
</body>
</html>
