<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_auth.php';
require_admin();

$projects = mysqli_query($conn, "SELECT id,title,description,file_path,created_at FROM projects ORDER BY created_at DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin — Projects</title>
  <style>
    body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0}
    .container{max-width:900px;margin:30px auto;background:#fff;border:1px solid #ddd;padding:20px;border-radius:6px}
    header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
    .btn{display:inline-block;padding:8px 12px;border-radius:4px;text-decoration:none;color:#fff;background:#4f46e5}
    .btn.gray{background:#666}
    .btn.red{background:#c0392b}
    table{width:100%;border-collapse:collapse}
    th,td{padding:10px;border-bottom:1px solid #eee;vertical-align:top}
    th{text-align:left;background:#fafafa}
    .row-actions a{margin-right:6px}
  </style>
</head>
<body>
  <div class="container">
    <header>
      <h1 style="margin:0;font-size:22px">Projects</h1>
      <div>
        <a class="btn" href="create.php">+ Add Project</a>
        <a class="btn gray" href="/MyPersonal_Porfolio/profile.php" target="_blank">View Site</a>
        <a class="btn red" href="logout.php">Logout</a>
      </div>
    </header>

    <table>
      <thead>
        <tr>
          <th style="width:22%">Title</th>
          <th>Description</th>
          <th style="width:20%">File</th>
          <th style="width:14%">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($p = mysqli_fetch_assoc($projects)): ?>
        <tr>
          <td><?php echo h($p['title']); ?></td>
          <td><?php echo nl2br(h($p['description'])); ?></td>
          <td>
            <?php if ($p['file_path']): ?>
              <a href="<?php echo h($p['file_path']); ?>" target="_blank">View</a>
            <?php else: ?>
              <span style="color:#6b7280">—</span>
            <?php endif; ?>
          </td>
          <td class="row-actions">
            <a class="btn" href="edit.php?id=<?php echo (int)$p['id']; ?>">Edit</a>
            <a class="btn red" href="delete.php?id=<?php echo (int)$p['id']; ?>" onclick="return confirm('Delete this project?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>


