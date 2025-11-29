<?php
require_once __DIR__ . '/db.php';

$projectId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$projectId) {
    header('Location: profile.php');
    exit;
}

$result = $conn->query("SELECT * FROM projects WHERE id = $projectId LIMIT 1");
$project = $result->fetch_assoc();

if (!$project) {
    header('Location: profile.php');
    exit;
}

$isImage = false;
$fileExt = '';
if ($project['file_path']) {
    $fileExt = strtolower(pathinfo($project['file_path'], PATHINFO_EXTENSION));
    $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo htmlspecialchars($project['title']); ?> — Project</title>
  <link rel="stylesheet" href="style/profile.css" />
  <style>
    .project-view {
      max-width: 900px;
      margin: 40px auto;
      padding: 20px;
    }
    .project-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }
    .project-header h1 {
      margin: 0;
      font-size: 28px;
      color: #fff;
    }
    .download-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 24px;
      background: var(--accent);
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      transition: background 0.3s;
    }
    .download-btn:hover {
      background: #6366f1;
    }
    .project-description {
      background: rgba(255, 255, 255, 0.08);
      padding: 24px;
      border-radius: 8px;
      margin-bottom: 24px;
      color: #e6eef8;
      line-height: 1.8;
      font-size: 15px;
      border-left: 4px solid var(--accent);
    }
    .project-description-title {
      font-size: 18px;
      font-weight: 600;
      color: #fff;
      margin-bottom: 12px;
      display: block;
    }
    .project-preview {
      background: rgba(255, 255, 255, 0.05);
      padding: 20px;
      border-radius: 8px;
      text-align: center;
    }
    .project-preview img {
      max-width: 100%;
      max-height: 600px;
      border-radius: 8px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }
    .file-icon {
      width: 80px;
      height: 80px;
      margin: 20px auto;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      font-size: 32px;
    }
    .back-link {
      display: inline-block;
      margin-top: 20px;
      color: var(--muted);
      text-decoration: none;
      font-size: 14px;
    }
    .back-link:hover {
      color: #fff;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="project-view">
      <div class="project-header">
        <h1><?php echo htmlspecialchars($project['title']); ?></h1>
        <?php if ($project['file_path']): ?>
          <a href="<?php echo htmlspecialchars($project['file_path']); ?>" download class="download-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Download File
          </a>
        <?php endif; ?>
      </div>

      <div class="project-description">
        <span class="project-description-title">Description</span>
        <?php echo nl2br(htmlspecialchars($project['description'])); ?>
      </div>

      <?php if ($project['file_path']): ?>
        <div class="project-preview">
          <?php if ($isImage): ?>
            <img src="/MyPersonal_Porfolio/<?php echo htmlspecialchars($project['file_path']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
          <?php else: ?>
            <div class="file-icon">
              <?php
              $iconMap = [
                'pdf' => '📄',
                'doc' => '📝',
                'docx' => '📝',
                'zip' => '📦',
                'rar' => '📦',
                '7z' => '📦',
                'mp4' => '🎥',
                'mp3' => '🎵',
              ];
              echo $iconMap[$fileExt] ?? '📎';
              ?>
            </div>
            <p style="color: var(--muted); margin-top: 10px;">File: <?php echo htmlspecialchars(basename($project['file_path'])); ?></p>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <a href="profile.php#projects" class="back-link">← Back to Projects</a>
    </div>
  </div>
</body>
</html>

