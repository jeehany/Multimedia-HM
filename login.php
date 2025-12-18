<?php
require_once 'config.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $q = mysqli_query($conn, "SELECT * FROM users WHERE email = '" . mysqli_real_escape_string($conn, $email) . "' LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $u = mysqli_fetch_assoc($q);
        if ($u['password'] === md5($password)) {
          $_SESSION['user'] = $u;
            header('Location: index.php');
            exit;
        } else {
            $err = 'Username atau password salah.';
        }
    } else {
        $err = 'Username atau password salah.';
    }
}

include 'header.php';
?>
  <div class="login-card">
    <div class="logo">
      <i class="fas fa-play-circle" style="font-size: 3rem; color: #4f46e5; margin-bottom: 0.5rem;"></i>
      <h1>HM Multimedia</h1>
      <p>Sistem Manajemen Alat Multimedia</p>
    </div>
    
    <?php if($err): ?>
    <div class="alert alert-danger">
      <i class="fas fa-exclamation-circle me-2"></i><?=$err?>
    </div>
    <?php endif; ?>
    
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <div class="input-group">
          <span class="input-group-text" style="background: #f8fafc; border-right: none;">
            <i class="fas fa-envelope" style="color: #94a3b8;"></i>
          </span>
          <input type="email" name="email" class="form-control" placeholder="Masukkan email" required style="border-left: none;">
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text" style="background: #f8fafc; border-right: none;">
            <i class="fas fa-lock" style="color: #94a3b8;"></i>
          </span>
          <input type="password" name="password" class="form-control" placeholder="Masukkan password" required style="border-left: none;">
        </div>
      </div>
      <button class="btn btn-primary w-100 btn-lg" type="submit">
        <i class="fas fa-sign-in-alt me-2"></i>Masuk
      </button>
    </form>
    
    <div class="text-center mt-4 pt-3" style="border-top: 1px solid #e2e8f0;">
      <small style="color: #94a3b8;">
        <i class="fas fa-info-circle me-1"></i>
        Demo: gunakan email dan password yang sesuai akun pada tabel `users`.
      </small>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
