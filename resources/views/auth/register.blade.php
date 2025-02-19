<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login & Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .container {
            margin-top: 50px;
            max-width: 400px;
        }
        .card {
            border-radius: 10px;
        }
        .btn-custom {
            width: 100%;
        }
        .text-small {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow p-4">   
            <h3 class="text-center">Admin Registration</h3>
            <form method="POST" action= "{{ route ('register') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">username</label>
                    <input type="username" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="register" class="btn btn-success btn-custom">Register</button>
            </form>
            <p class="text-center text-small mt-3">
                Already have an account? <a href="admin.php">Login here</a>
            </p>
            <h3 class="text-center">Admin Login</h3>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">username</label>
                    <input type="username" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary btn-custom">Login</button>
            </form>
            <p class="text-center text-small mt-3">
                Don't have an account? <a href="admin.php?register=true">Register here</a>
            </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>