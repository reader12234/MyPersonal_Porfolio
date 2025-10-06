<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_auth.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: /admin/index.php'); exit; }

$res = mysqli_query($conn, "SELECT id,title,description,file_path FROM projects WHERE id=" . $id);
$project = mysqli_fetch_assoc($res);
if (!$project) { header('Location: /admin/index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title == '' || $description == '') {
        $error = 'Title and description are required';
    } else {
        $filePath = $project['file_path'];
        if (!empty($_FILES['file']['name'])) {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $safeName = uniqid('proj_', true) . ($ext ? ('.' . strtolower($ext)) : '');
            $dest = $uploadDir . $safeName;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
                if ($filePath && is_file(__DIR__ . '/../' . $filePath)) { @unlink(__DIR__ . '/../' . $filePath); }
                $filePath = 'uploads/' . $safeName;
            } else {
                $error = 'Failed to upload file';
            }
        }

        if ($error == '') {
            $titleEsc = mysqli_real_escape_string($conn, $title);
            $descEsc = mysqli_real_escape_string($conn, $description);
            $fileEsc = $filePath ? ("'" . mysqli_real_escape_string($conn, $filePath) . "'") : 'NULL';
            $sql = "UPDATE projects SET title='{$titleEsc}', description='{$descEsc}', file_path={$fileEsc} WHERE id={$id}";
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
  <title>Edit Project</title>
  <link rel="stylesheet" href="../style/admin.css" />
</head>
<body>
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <h1 style="margin:0;font-size:22px">Edit Project</h1>
      <a class="btn" href="/admin/index.php">← Back</a>
    </div>
    <div class="card">
      <?php if ($error): ?><div class="error"><?php echo h($error); ?></div><?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <div class="field">
          <label>Title *</label>
          <input name="title" value="<?php echo h($project['title']); ?>" required>
        </div>
        <div class="field">
          <label>Description *</label>
          <textarea name="description" rows="6" required><?php echo h($project['description']); ?></textarea>
        </div>
        <div class="field">
          <label>Attachment (optional)</label>
          <?php if ($project['file_path']): ?>
            <div class="muted">Current: <a style="color:#93c5fd" target="_blank" href="<?php echo h($project['file_path']); ?>">View</a></div>
          <?php endif; ?>
          <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.gif,.zip,.rar,.7z,.doc,.docx,.ppt,.pptx,.mp4">
        </div>
        <button class="btn" type="submit">Update</button>
      </form>
    </div>
  </div>
</body>
</html>


