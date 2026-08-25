<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?php echo $_SESSION['com_name'] ?? 'My Hospital'; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        /* 🌄 Background image fullscreen */
        body {
            background: 
                linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('assets/images/bg-auth.jpg') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        /* Fallback background color if image doesn't load */
        body {
            background-color: #f8f9fa;
        }

        /* Ensure background covers entire viewport */
        @media (max-width: 768px) {
            body {
                background-attachment: fixed;
            }
        }

        /* 💎 Login box styling */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px 30px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            backdrop-filter: blur(5px);
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 576px) {
            .login-card {
                padding: 30px 20px;
                border-radius: 15px;
                max-width: 100%;
            }
            
            .company-title {
                font-size: 20px !important;
            }
            
            .logo img {
                width: 100px;
            }
        }

        /* Extra small devices */
        @media (max-width: 375px) {
            .login-card {
                padding: 25px 15px;
            }
            
            .company-title {
                font-size: 18px !important;
            }
            
            .text-muted {
                font-size: 13px;
            }
            
            body {
                padding: 10px;
            }
        }

        /* Large screens */
        @media (min-width: 1200px) {
            .login-card {
                max-width: 450px;
                padding: 50px 40px;
            }
        }

        /* Tablet screens */
        @media (min-width: 768px) and (max-width: 1199px) {
            .login-card {
                max-width: 400px;
            }
        }

        .login-card h4 {
            color: #333;
            font-weight: 600;
        }

        .login-card .btn-login {
            background: #ec3237;
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            transition: 0.3s;
            padding: 12px;
            font-size: 16px;
        }

        .login-card .btn-login:hover {
            background: #c72025;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .login-card .btn-login:active {
            transform: translateY(0);
        }

        .logo img {
            width: 120px;
            margin-bottom: 15px;
            max-width: 100%;
            height: auto;
        }

        .text-muted {
            font-size: 14px;
            line-height: 1.5;
        }

        .company-title {
            font-size: 25px;
            font-weight: 600;
            color: #222;
            line-height: 1.3;
            margin-bottom: 10px;
        }

        /* Form input responsiveness */
        .form-control {
            padding: 12px 15px;
            font-size: 16px; /* Prevents zoom on iOS */
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            border-color: #ec3237;
            box-shadow: 0 0 0 0.2rem rgba(236, 50, 55, 0.25);
        }

        @media (max-width: 576px) {
            .form-control {
                padding: 10px 12px;
                font-size: 15px;
            }
        }

        /* Label responsiveness */
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        @media (max-width: 576px) {
            .form-label {
                font-size: 13px;
            }
        }

        /* Error message styling */
        .text-danger {
            font-size: 14px;
            min-height: 20px;
            font-weight: 500;
        }

        /* Loading state for background image */
        .loading-background {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        }
    </style>
</head>
<body>

    <div class="login-card text-center">
        <div class="logo">
            <img src="<?php echo 'assets/images/' . ($_SESSION['com_logo'] ?? 'default-logo.png'); ?>" alt="Logo" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiByeD0iMTAiIGZpbGw9IiNlYzMyMzciLz4KPHRleHQgeD0iNjAiIHk9IjY1IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5Mb2dvPC90ZXh0Pgo8L3N2Zz4K'">
        </div>
        <div class="company-title">
            <?php echo $_SESSION['com_name'] ?? 'Hospital'; ?> & Dental Hospital Depalpur
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
                    if (isset($_SESSION['loginfail'])) echo $_SESSION['loginfail'];
                    if (isset($_SESSION['msg'])) echo $_SESSION['msg'];
                    
                    // Clear the messages after displaying
                    unset($_SESSION['loginfail']);
                    unset($_SESSION['msg']);
                ?>
            </div>

            <div class="d-grid">
                <button class="btn btn-login" name="logintyww" type="submit">
                    <i class="fa fa-sign-in-alt"></i> Sign In
                </button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Enhance background image loading
        document.addEventListener('DOMContentLoaded', function() {
            const bgImage = new Image();
            bgImage.src = 'assets/images/bg-auth.jpg'; // Fixed path to match CSS
            
            bgImage.onload = function() {
                document.body.classList.remove('loading-background');
                console.log('Background image loaded successfully');
            };
            
            bgImage.onerror = function() {
                console.log('Background image failed to load, using fallback background');
                document.body.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            };
        });
    </script>
</body>
</html>
