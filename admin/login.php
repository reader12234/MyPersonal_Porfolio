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
    <link rel="stylesheet" href="../style/admin.css" />
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
      <div class="muted"><a href="/MyPersonal_Porfolio/index.html" style="color:#9ca3af">← Back to Portfolio</a></div>
    </div>
  </body>
</html>


