<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SS Hospital</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('assets/images/bg-auth.jpg') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px 30px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        .login-card .btn-login {
            background: #ec3237;
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            padding: 12px;
            font-size: 16px;
            width: 100%;
        }
        .login-card .btn-login:hover {
            background: #c72025;
        }
        .logo img { width: 120px; margin-bottom: 15px; }
        .company-title { font-size: 25px; font-weight: 600; color: #222; }
        .form-control { padding: 12px 15px; font-size: 16px; border-radius: 8px; }
        @media (max-width: 576px) {
            .login-card { padding: 30px 20px; }
            .company-title { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="login-card text-center">
        <div class="logo">
            <img src="images/default-logo.png" alt="Logo" onerror="this.style.display='none'">
        </div>
        <div class="company-title">
            SS Hospital & Dental Hospital Depalpur
        </div>
        <p class="text-muted mb-4">Enter your username or email and password</p>
        <form action="models/login.php" method="post">
            <div class="mb-3 text-start">
                <label for="emailaddress" class="form-label">Username or Email</label>
                <input class="form-control" name="userjsd" type="text" id="emailaddress" required placeholder="Enter your email">
            </div>
            <div class="mb-3 text-start">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="passjfbdj" id="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <div class="text-danger mb-3">
                <?php 
                    if (isset($_SESSION['loginfail'])) {
                        echo $_SESSION['loginfail'];
                        unset($_SESSION['loginfail']);
                    }
                    if (isset($_SESSION['msg'])) {
                        echo $_SESSION['msg'];
                        unset($_SESSION['msg']);
                    }
                ?>
            </div>
            <div class="d-grid">
                <button class="btn btn-login" name="logintyww" type="submit">
                    <i class="fa fa-sign-in-alt"></i> Sign In
                </button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
