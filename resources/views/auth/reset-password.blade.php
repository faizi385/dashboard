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
        <h2 class="form-header text-center">Login to Your Account</h2>
        <p class="form-description text-center">Enter your email and password to continue.</p>

        <form id="loginForm" method="post" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label required">Email <span class="text-danger">*</span></label>
                <input id="email" readonly type="email" name="email" value="{{ old('email', $updatePassword->email) }}" autofocus 
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label required">New Password <span class="text-danger">*</span></label>
                <input id="password" type="password" name="password" 
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label required">Confirm Password <span class="text-danger">*</span></label>
                <input id="password" type="password" name="password_confirmation" 
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Forgot Password and Submit Button -->
            <div class="d-flex justify-content-between align-items-center">
                @if (Route::has('password.request'))
                    <a style="text-decoration: none" href="{{ route('password.request') }}" class="text-link ">Forgot your password?</a>
                @endif
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
    $(document).on('click', '.view_password_for_reset_page', function() {
        let input = $(this).parent().find(".pass");
        input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
        let inputShow = $(this).parent().find(".view_password_for_reset_page");
        console.log(inputShow);
        inputShow.attr('class', input.attr('type') === 'password' ? 'fa-solid fa-eye-slash view_password_for_reset_page' :
            'fa-solid fa-eye view_password_for_reset_page');
    });
</script>
</body>
</html>
