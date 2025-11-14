<?php
require_once 'config.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $q = mysqli_query($conn, "SELECT * FROM tabel_user WHERE username = '" . mysqli_real_escape_string($conn, $username) . "' LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $u = mysqli_fetch_assoc($q);
        // Demo uses MD5 hashes (from install.sql)
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
<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Login</h4>
          <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary">Login</button>
          </form>
          <hr>
          <p>Contoh akun: admin/admin123, kepala/kepala123</p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
