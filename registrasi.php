<?php
include "config/koneksi.php";

$pesan = "";

if(isset($_POST['daftar'])){
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // VALIDASI
    if(strlen($password) < 5){
        $pesan = "Password minimal 5 karakter!";
    }else{
        // CEK USERNAME
        $cek = mysqli_query($koneksi,"SELECT id FROM users WHERE username='$username'");
        if(mysqli_num_rows($cek) > 0){
            $pesan = "Username sudah digunakan! Silakan gunakan username lain.";
        }else{
            // HASH PASSWORD
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // SIMPAN (DEFAULT: pengunjung)
            $simpan = mysqli_query($koneksi,"
                INSERT INTO users (nama, username, password, role)
                VALUES ('$nama','$username','$hash','pengunjung')
            ");

            if($simpan){
                header("Location: login.php?daftar=success");
                exit;
            }else{
                $pesan = "Gagal menyimpan data ke database!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun | Masjid Al-Ikhlas</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Plus Jakarta Sans & Amiri -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #0d6efd;
            --success-green: #198754;
            --emerald-teal: #20c997;
            --dark-emerald: #0f2d24;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #061826 0%, #09203f 50%, #0c3325 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 24px 16px;
        }

        /* Subtle Islamic Geometric Overlay Pattern */
        .bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: radial-gradient(rgba(13, 110, 253, 0.12) 1.5px, transparent 1.5px), radial-gradient(rgba(32, 201, 151, 0.1) 1.5px, transparent 1.5px);
            background-size: 36px 36px;
            background-position: 0 0, 18px 18px;
            pointer-events: none;
            z-index: 0;
        }

        /* Ambient Glow Spheres */
        .glow-sphere-1, .glow-sphere-2 {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.45;
            pointer-events: none;
            z-index: 0;
        }
        .glow-sphere-1 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, #20c997 0%, rgba(32, 201, 151, 0) 70%);
            top: -100px;
            left: -100px;
            animation: floatGlow 14s ease-in-out infinite alternate;
        }
        .glow-sphere-2 {
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, #0d6efd 0%, rgba(13, 110, 253, 0) 70%);
            bottom: -120px;
            right: -120px;
            animation: floatGlow 12s ease-in-out infinite alternate-reverse;
        }

        @keyframes floatGlow {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -20px) scale(1.1); }
            100% { transform: translate(-20px, 30px) scale(0.95); }
        }

        /* Glassmorphism Card */
        .register-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.45),
                        0 0 40px rgba(13, 110, 253, 0.15);
            position: relative;
            z-index: 10;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #198754, #0d6efd, #20c997);
        }

        /* Brand Badge */
        .brand-icon-wrapper {
            width: 76px;
            height: 76px;
            background: linear-gradient(135deg, #e3f2fd 0%, #e8f5e9 100%);
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 25px rgba(13, 110, 253, 0.18),
                        inset 0 2px 4px rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(13, 110, 253, 0.15);
            position: relative;
            transform: rotate(3deg);
            transition: transform 0.3s ease;
        }
        .brand-icon-wrapper:hover {
            transform: rotate(0deg) scale(1.05);
        }

        .bismillah-text {
            font-family: 'Amiri', serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #0d6efd;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            text-shadow: 0 2px 4px rgba(13, 110, 253, 0.1);
        }

        .input-label-custom {
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
            letter-spacing: 0.3px;
            margin-bottom: 6px;
        }

        .input-group-custom {
            border-radius: 16px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            transition: all 0.25s ease;
            overflow: hidden;
        }

        .input-group-custom:focus-within {
            border-color: #0d6efd;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
        }

        .input-group-text-custom {
            background: transparent;
            border: none;
            padding-left: 16px;
            padding-right: 12px;
            color: #0d6efd;
            font-size: 1.15rem;
        }

        .form-control-custom {
            background: transparent;
            border: none;
            padding: 13px 16px 13px 4px;
            font-weight: 600;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .form-control-custom::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .form-control-custom:focus {
            box-shadow: none;
            background: transparent;
        }

        .btn-eye {
            border: none;
            background: transparent;
            color: #64748b;
            padding-right: 16px;
            padding-left: 8px;
            transition: color 0.2s ease;
        }
        .btn-eye:hover {
            color: #0d6efd;
        }

        .btn-register-custom {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: #ffffff;
            border: none;
            border-radius: 16px;
            padding: 14px 24px;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            box-shadow: 0 10px 25px rgba(13, 110, 253, 0.32);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-register-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 32px rgba(13, 110, 253, 0.45);
            color: #ffffff;
        }

        .btn-register-custom:active {
            transform: translateY(0);
        }

        .btn-login-link {
            background: rgba(25, 135, 84, 0.06);
            border: 1.5px solid rgba(25, 135, 84, 0.4);
            color: #198754;
            border-radius: 16px;
            padding: 12px 24px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-login-link:hover {
            background: #198754;
            color: #ffffff;
            border-color: #198754;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(25, 135, 84, 0.3);
        }

        .btn-home-link {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 30px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .btn-home-link:hover {
            color: #198754;
            background: rgba(25, 135, 84, 0.08);
        }

        .alert-custom {
            border: none;
            border-radius: 14px;
            padding: 12px 16px;
            font-size: 0.875rem;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="bg-pattern"></div>
    <div class="glow-sphere-1"></div>
    <div class="glow-sphere-2"></div>

    <div class="container position-relative" style="z-index: 10;">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4 col-sm-10">

                <div class="card register-card">
                    <div class="card-body p-4 p-sm-5">

                        <!-- HEADER BRAND & TITLE -->
                        <div class="text-center mb-4">
                            <div class="brand-icon-wrapper mb-3">
                                <i class="bi bi-person-plus-fill text-primary fs-1"></i>
                            </div>
                            <div class="bismillah-text">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
                            <h4 class="fw-bold text-dark m-0 tracking-tight">Registrasi Akun</h4>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 mt-2 fw-semibold" style="font-size: 0.75rem;">
                                <i class="bi bi-person-check-fill me-1"></i> Pendaftaran Jamaah Baru
                            </span>
                        </div>

                        <!-- NOTIFIKASI ERROR -->
                        <?php if($pesan != ""){ ?>
                            <div class="alert alert-danger alert-custom text-start shadow-sm mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                                    <div><?= htmlspecialchars($pesan) ?></div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- FORM REGISTRASI -->
                        <form method="post">

                            <div class="mb-3">
                                <label class="input-label-custom">NAMA LENGKAP</label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text input-group-text-custom">
                                        <i class="bi bi-card-heading"></i>
                                    </span>
                                    <input type="text" name="nama" class="form-control form-control-custom"
                                           placeholder="Masukkan nama lengkap Anda" required autofocus autocomplete="name">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="input-label-custom">USERNAME</label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text input-group-text-custom">
                                        <i class="bi bi-person-fill"></i>
                                    </span>
                                    <input type="text" name="username" class="form-control form-control-custom"
                                           placeholder="Buat username login" required autocomplete="username">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="input-label-custom">PASSWORD</label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text input-group-text-custom">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input type="password" name="password" id="inputPassword" class="form-control form-control-custom"
                                           placeholder="Minimal 5 karakter" required autocomplete="new-password">
                                    <button class="btn btn-eye" type="button" onclick="togglePassword()" title="Tampilkan/Sembunyikan Password">
                                        <i class="bi bi-eye-slash-fill" id="eyeIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <button name="daftar" type="submit" class="btn btn-register-custom w-100">
                                <i class="bi bi-check-circle-fill me-1 fs-5 align-middle"></i> DAFTAR SEKARANG
                            </button>

                        </form>

                        <!-- SEPARATOR & LOGIN LINK -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="text-secondary small mb-2 font-weight-500">Sudah memiliki akun?</p>
                            <a href="login.php" class="btn btn-login-link w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i> MASUK KE AKUN
                            </a>
                        </div>

                        <!-- FOOTER HOME LINK -->
                        <div class="text-center mt-4 pt-2">
                            <a href="index.php" class="btn-home-link">
                                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                            </a>
                            <div class="text-muted small mt-2 opacity-75" style="font-size: 0.75rem;">
                                © <?= date('Y') ?> Masjid Al-Ikhlas Digital. Hak Cipta Dilindungi.
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toggle Password Visibility Script -->
    <script>
        function togglePassword() {
            const pwdInput = document.getElementById("inputPassword");
            const eyeIcon = document.getElementById("eyeIcon");
            if (pwdInput.type === "password") {
                pwdInput.type = "text";
                eyeIcon.className = "bi bi-eye-fill text-primary";
            } else {
                pwdInput.type = "password";
                eyeIcon.className = "bi bi-eye-slash-fill";
            }
        }
    </script>
</body>
</html>
