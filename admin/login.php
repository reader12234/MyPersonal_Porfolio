<?php
require_once __DIR__ . '/../db.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $sql = "SELECT id, password FROM admins WHERE username='" . mysqli_real_escape_string($conn, $username) . "' LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if ($res && $row = mysqli_fetch_assoc($res)) {
        if ($password === $row['password']) {
            $_SESSION['admin_id'] = $row['id'];
            header('Location: /MyPersonal_Porfolio/admin/index.php');
            exit;
        }
    }
    $error = 'Invalid username or password';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login</title>
    <style>
      body{font-family:Arial,sans-serif;background:#eee;margin:0;padding:0}
      .wrap{max-width:380px;margin:60px auto;background:#fff;border:1px solid #ddd;padding:20px;border-radius:6px}
      h1{font-size:20px;margin:0 0 12px 0}
      .field{margin-bottom:10px}
      input{width:100%;padding:10px;border:1px solid #ccc;border-radius:4px}
      .btn{width:100%;padding:10px;background:#4f46e5;color:#fff;border:none;border-radius:4px;cursor:pointer}
      .error{color:#c0392b;margin-bottom:8px;font-size:14px}
      .muted{font-size:12px;color:#666;text-align:center;margin-top:10px}
      a{color:#666;text-decoration:none}
    </style>
  </head>
  <body>
    <div class="wrap">
      <h1>Admin Login</h1>
      <?php if ($error): ?><div class="error"><?php echo h($error); ?></div><?php endif; ?>
      <form method="post">
        <div class="field"><input name="username" placeholder="Username" required></div>
        <div class="field"><input type="password" name="password" placeholder="Password" required></div>
        <button class="btn" type="submit">Login</button>
      </form>
      <div class="muted"><a href="../index.html">← Back to Portfolio</a></div>
    </div>
  </body>
</html>


