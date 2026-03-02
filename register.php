<?php

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Auth.php';

// Redirect to dashboard if already logged in
$auth = new Auth((new Database())->getConnection());
if ($auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// Handle registration request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nrp = trim($_POST['nrp'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($nrp) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak sama';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        try {
            $db = new Database();
            $auth = new Auth($db->getConnection());
            
            // Check if NRP is valid
            if (!$auth->isNRPValid($nrp)) {
                $error = 'NRP tidak ditemukan atau tidak aktif. Hubungi admin.';
            } elseif ($auth->isNRPRegistered($nrp)) {
                $error = 'NRP ini sudah terdaftar. Silakan login.';
            } else {
                // Register user
                $auth->register($nrp, $password);
                $success = 'Registrasi berhasil! Silakan login dengan NRP dan password Anda.';
                
                // Auto login after registration
                if ($auth->login($nrp, $password)) {
                    header('Location: index.php');
                    exit();
                }
            }
        } catch(Exception $e) {
            $error = $e->getMessage();
            error_log('Registration error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Aplikasi BAGOPS Polres Samosir</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-section h1 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .logo-section p {
            color: #7f8c8d;
            margin-bottom: 0;
            font-size: 14px;
        }
        .form-label {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-control {
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px;
            margin-bottom: 20px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e1e8ed;
        }
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .police-badge {
            color: #667eea;
            font-size: 48px;
            margin-bottom: 20px;
        }
        .info-text {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="logo-section">
                <i class="fas fa-shield-alt police-badge"></i>
                <h1>BAGOPS POLRES SAMOSIR</h1>
                <p>Sistem Informasi Manajemen Operasional</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <div class="info-text">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Informasi:</strong> Hanya personil Polres Samosir yang terdaftar dapat mendaftar. Gunakan NRP resmi Anda.
            </div>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nrp" class="form-label">
                        <i class="fas fa-id-card me-2"></i>NRP (Nomor Register Personil)
                    </label>
                    <input type="text" class="form-control" id="nrp" name="nrp" 
                           value="<?= htmlspecialchars($_POST['nrp'] ?? '') ?>" 
                           placeholder="Masukkan NRP Anda" required>
                    <div class="form-text">NRP harus sesuai dengan data personil Polres Samosir</div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Masukkan password (minimal 6 karakter)" required>
                </div>
                
                <div class="mb-4">
                    <label for="confirm_password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Konfirmasi Password
                    </label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                           placeholder="Ulangi password" required>
                </div>
                
                <button type="submit" class="btn btn-register">
                    <i class="fas fa-user-plus me-2"></i>DAFTAR AKUN
                </button>
            </form>
            
            <div class="login-link">
                <p class="mb-0">Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>
    </div>
    
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Password tidak sama');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // NRP validation (numeric only)
        document.getElementById('nrp').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
