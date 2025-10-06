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
  <link rel="stylesheet" href="../style/admin.css" />
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


