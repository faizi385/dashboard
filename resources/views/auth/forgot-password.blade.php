
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }

        .form-header {
            font-size: 1.8rem;
            font-weight: bold;
            color: #343a40;
        }

        .form-description {
            color: #6c757d;
            margin-bottom: 1rem;
        }

        .btn-primary {
            background: #007bff;
            border: none;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .text-link {
            color: #007bff;
        }

        .text-link:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

    <div class="form-container">
        <h2 class="form-header text-center">Reset Your Account</h2>
        <p class="form-description text-center">Enter your email </p>

        <form id="loginForm" method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label required">Email <span class="text-danger">*</span></label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus 
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            <!-- Forgot Password and Submit Button -->
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-primary">Reset</button>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Show success or error messages using Toastr
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            @if (session('pending_invitation'))
                toastr.warning("{{ session('pending_invitation') }}");
            @endif
        });

        // JavaScript for client-side validation
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            let email = document.getElementById('email');
            let password = document.getElementById('password');
            let isValid = true;

            // Clear previous error styles
            email.classList.remove('is-invalid');
            password.classList.remove('is-invalid');
            email.nextElementSibling?.classList.remove('invalid-feedback');
            password.nextElementSibling?.classList.remove('invalid-feedback');

            // Validate email
            if (email.value.trim() === '') {
                isValid = false;
                email.classList.add('is-invalid');
                let emailError = document.createElement('div');
                emailError.classList.add('invalid-feedback');
                emailError.textContent = 'Email is required.';
                email.parentNode.appendChild(emailError);
            }

            // Validate password
            if (password.value.trim() === '') {
                isValid = false;
                password.classList.add('is-invalid');
                let passwordError = document.createElement('div');
                passwordError.classList.add('invalid-feedback');
                passwordError.textContent = 'Password is required.';
                password.parentNode.appendChild(passwordError);
            }

            // If validation fails, prevent form submission
            if (!isValid) {
                event.preventDefault();
            }
        });

        // Remove validation errors when the user types
        document.getElementById('email').addEventListener('input', function() {
            let email = document.getElementById('email');
            email.classList.remove('is-invalid');
            let emailError = email.parentNode.querySelector('.invalid-feedback');
            if (emailError) {
                emailError.remove();
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            let password = document.getElementById('password');
            password.classList.remove('is-invalid');
            let passwordError = password.parentNode.querySelector('.invalid-feedback');
            if (passwordError) {
                passwordError.remove();
            }
        });
    </script>
</body>
</html>

