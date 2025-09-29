<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_auth.php';
require_admin();

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title == '' || $description == '') {
        $error = 'Title and description are required';
    } else {
        $filePath = null;
        if (!empty($_FILES['file']['name'])) {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $safeName = uniqid('proj_', true) . ($ext ? ('.' . strtolower($ext)) : '');
            $dest = $uploadDir . $safeName;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
                $filePath = '/uploads/' . $safeName;
            } else {
                $error = 'Failed to upload file';
            }
        }

        if ($error == '') {
            $titleEsc = mysqli_real_escape_string($conn, $title);
            $descEsc = mysqli_real_escape_string($conn, $description);
            $fileEsc = $filePath ? ("'" . mysqli_real_escape_string($conn, $filePath) . "'") : 'NULL';
            $sql = "INSERT INTO projects (title, description, file_path) VALUES ('{$titleEsc}', '{$descEsc}', {$fileEsc})";
            mysqli_query($conn, $sql);
            header('Location: /MyPersonal_Porfolio/admin/index.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Project</title>
  <style>
    body{margin:0;font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;background:#0f172a;color:#e6eef8}
    .container{max-width:800px;margin:32px auto;padding:24px}
    a.btn,button.btn{display:inline-block;padding:.5rem .9rem;border-radius:8px;text-decoration:none;color:#fff;background:#4f46e5;border:none;cursor:pointer}
    .card{background:#0b1220;padding:18px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.4)}
    .field{margin-bottom:12px}
    input,textarea{width:100%;padding:10px;border-radius:8px;border:1px solid rgba(255,255,255,.08);background:transparent;color:#e6eef8}
    label{display:block;margin:6px 0;font-weight:600;color:#cfe8ff}
    .muted{color:#6b7280}
    .error{color:#f87171;margin-bottom:12px}
  </style>
</head>
<body>
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <h1 style="margin:0;font-size:22px">Add Project</h1>
      <a class="btn" href="/admin/index.php">← Back</a>
    </div>
    <div class="card">
      <?php if ($error): ?><div class="error"><?php echo h($error); ?></div><?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <div class="field">
          <label>Title *</label>
          <input name="title" required>
        </div>
        <div class="field">
          <label>Description *</label>
          <textarea name="description" rows="6" required></textarea>
        </div>
        <div class="field">
          <label>Attachment (optional)</label>
          <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.gif,.zip,.rar,.7z,.doc,.docx,.ppt,.pptx,.mp4">
          <div class="muted">Allowed: documents, images, archives, videos</div>
        </div>
        <button class="btn" type="submit">Save</button>
      </form>
    </div>
  </div>
</body>
</html>


