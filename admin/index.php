<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_auth.php';
require_admin();

// Get current settings
$avatarResult = mysqli_query($conn, "SELECT setting_value FROM profile_settings WHERE setting_key='avatar_path' LIMIT 1");
$avatarRow = mysqli_fetch_assoc($avatarResult);
$currentAvatar = $avatarRow ? $avatarRow['setting_value'] : null;

$introResult = mysqli_query($conn, "SELECT setting_value FROM profile_settings WHERE setting_key='intro_text' LIMIT 1");
$introRow = mysqli_fetch_assoc($introResult);
$currentIntro = $introRow ? $introRow['setting_value'] : '';

$educationResult = mysqli_query($conn, "SELECT setting_value FROM profile_settings WHERE setting_key='education_text' LIMIT 1");
$educationRow = mysqli_fetch_assoc($educationResult);
$currentEducation = $educationRow ? $educationRow['setting_value'] : '';

$servicesResult = mysqli_query($conn, "SELECT setting_value FROM profile_settings WHERE setting_key='services_text' LIMIT 1");
$servicesRow = mysqli_fetch_assoc($servicesResult);
$currentServices = $servicesRow ? $servicesRow['setting_value'] : '';

$error = '';
$success = '';
$action = $_GET['action'] ?? 'list';
$editingProjectId = null;
$editingProject = null;

// Handle Project Deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_project_id'])) {
    $deleteId = (int)$_POST['delete_project_id'];
    $res = mysqli_query($conn, "SELECT file_path FROM projects WHERE id=" . $deleteId);
    if ($row = mysqli_fetch_assoc($res)) {
        mysqli_query($conn, "DELETE FROM projects WHERE id=" . $deleteId);
        if (!empty($row['file_path'])) {
            $p = __DIR__ . '/../' . $row['file_path'];
            if (is_file($p)) { @unlink($p); }
        }
        $success = 'Project deleted successfully!';
        $action = 'list';
    }
}

// Handle Project Creation/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['project_submit'])) {
    $projectId = (int)($_POST['project_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title == '' || $description == '') {
        $error = 'Title and description are required';
    } else {
        $filePath = null;
        
        // If editing, get existing file path
        if ($projectId > 0) {
            $res = mysqli_query($conn, "SELECT file_path FROM projects WHERE id=" . $projectId);
            if ($row = mysqli_fetch_assoc($res)) {
                $filePath = $row['file_path'];
            }
        }
        
        if (!empty($_FILES['file']['name'])) {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $originalName = $_FILES['file']['name'];
            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $safeBaseName = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $baseName);
            $safeBaseName = preg_replace('/\s+/', '_', trim($safeBaseName));
            $safeName = $safeBaseName . ($ext ? ('.' . strtolower($ext)) : '');
            
            $counter = 1;
            $finalName = $safeName;
            while (file_exists($uploadDir . $finalName)) {
                $finalName = $safeBaseName . '_' . $counter . ($ext ? ('.' . strtolower($ext)) : '');
                $counter++;
            }
            
            $dest = $uploadDir . $finalName;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
                if ($filePath && is_file(__DIR__ . '/../' . $filePath)) { @unlink(__DIR__ . '/../' . $filePath); }
                $filePath = 'uploads/' . $finalName;
            } else {
                $error = 'Failed to upload file';
            }
        }

        if ($error == '') {
            $titleEsc = mysqli_real_escape_string($conn, $title);
            $descEsc = mysqli_real_escape_string($conn, $description);
            $fileEsc = $filePath ? ("'" . mysqli_real_escape_string($conn, $filePath) . "'") : 'NULL';
            
            if ($projectId > 0) {
                // Update existing project
                $sql = "UPDATE projects SET title='{$titleEsc}', description='{$descEsc}', file_path={$fileEsc} WHERE id={$projectId}";
                $success = 'Project updated successfully!';
            } else {
                // Create new project
                $sql = "INSERT INTO projects (title, description, file_path) VALUES ('{$titleEsc}', '{$descEsc}', {$fileEsc})";
                $success = 'Project created successfully!';
            }
            mysqli_query($conn, $sql);
            $action = 'list';
        }
    }
}

// Handle settings form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['settings_submit'])) {
    // Handle avatar upload
    if (!empty($_FILES['avatar']['name'])) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['avatar']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            $error = 'Invalid file type. Only images (JPEG, PNG, GIF, WebP) are allowed.';
        } else {
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $safeName = 'profile.' . strtolower($ext);
            $dest = $uploadDir . $safeName;
            
            if ($currentAvatar && file_exists(__DIR__ . '/../' . $currentAvatar)) {
                $oldPath = __DIR__ . '/../' . $currentAvatar;
                $oldExt = pathinfo($currentAvatar, PATHINFO_EXTENSION);
                $backupName = 'profile_backup_' . date('YmdHis') . '.' . $oldExt;
                $backupPath = $uploadDir . $backupName;
                if (strpos(basename($currentAvatar), 'profile_backup_') !== 0) {
                    @rename($oldPath, $backupPath);
                }
            }
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                $filePath = 'uploads/' . $safeName;
                $filePathEsc = mysqli_real_escape_string($conn, $filePath);
                mysqli_query($conn, "INSERT INTO profile_settings (setting_key, setting_value) VALUES ('avatar_path', '{$filePathEsc}') ON DUPLICATE KEY UPDATE setting_value='{$filePathEsc}'");
                $currentAvatar = $filePath;
                $success = 'Profile picture uploaded successfully!';
            } else {
                $error = 'Failed to upload file';
            }
        }
    }
    
    if (isset($_POST['intro_text'])) {
        $introText = trim($_POST['intro_text']);
        $introEsc = mysqli_real_escape_string($conn, $introText);
        mysqli_query($conn, "INSERT INTO profile_settings (setting_key, setting_value) VALUES ('intro_text', '{$introEsc}') ON DUPLICATE KEY UPDATE setting_value='{$introEsc}'");
        $currentIntro = $introText;
        if ($success == '') {
            $success = 'Intro text updated successfully!';
        }
    }
    
    if (isset($_POST['education_text'])) {
        $educationText = trim($_POST['education_text']);
        $educationEsc = mysqli_real_escape_string($conn, $educationText);
        mysqli_query($conn, "INSERT INTO profile_settings (setting_key, setting_value) VALUES ('education_text', '{$educationEsc}') ON DUPLICATE KEY UPDATE setting_value='{$educationEsc}'");
        $currentEducation = $educationText;
        if ($success == '') {
            $success = 'Settings updated successfully!';
        }
    }
    
    if (isset($_POST['services_text'])) {
        $servicesText = trim($_POST['services_text']);
        $servicesEsc = mysqli_real_escape_string($conn, $servicesText);
        mysqli_query($conn, "INSERT INTO profile_settings (setting_key, setting_value) VALUES ('services_text', '{$servicesEsc}') ON DUPLICATE KEY UPDATE setting_value='{$servicesEsc}'");
        $currentServices = $servicesText;
        if ($success == '') {
            $success = 'Settings updated successfully!';
        }
    }
}

// Load project for editing
if ($action === 'edit') {
    $editingProjectId = (int)($_GET['id'] ?? 0);
    if ($editingProjectId > 0) {
        $res = mysqli_query($conn, "SELECT id,title,description,file_path FROM projects WHERE id=" . $editingProjectId);
        $editingProject = mysqli_fetch_assoc($res);
        if (!$editingProject) {
            $action = 'list';
            $editingProjectId = null;
        }
    } else {
        $action = 'list';
    }
}

$projects = mysqli_query($conn, "SELECT id,title,description,file_path,created_at FROM projects ORDER BY created_at DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../style/admin.css" />
  <style>
    .dashboard-tabs {
      display: flex;
      gap: 8px;
      margin-bottom: 24px;
      border-bottom: 1px solid #e5e7eb;
    }
    .tab-btn {
      padding: 12px 16px;
      background: none;
      border: none;
      cursor: pointer;
      font-size: 14px;
      font-weight: 500;
      color: #6b7280;
      border-bottom: 3px solid transparent;
      transition: all 0.2s;
    }
    .tab-btn:hover {
      color: #374151;
    }
    .tab-btn.active {
      color: #3b82f6;
      border-bottom-color: #3b82f6;
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
    .section-title {
      font-size: 18px;
      font-weight: 600;
      margin: 24px 0 16px 0;
      color: #1f2937;
    }
    .inline-form {
      display: flex;
      gap: 12px;
      align-items: flex-end;
      margin-top: 16px;
    }
    .inline-form .field {
      margin: 0;
      flex: 1;
    }
    .inline-form .btn {
      align-self: flex-end;
    }
  </style>
</head>
<body>
  <div class="container">
    <header>
      <h1 style="margin:0;font-size:24px">Admin Dashboard</h1>
      <div>
        <a class="btn gray" href="/MyPersonal_Porfolio/profile.php" target="_blank">View Site</a>
        <a class="btn red" href="logout.php">Logout</a>
      </div>
    </header>

    <?php if ($error): ?><div class="error" style="margin-bottom:16px"><?php echo h($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div style="background:#10b981;color:#fff;padding:12px;border-radius:6px;margin-bottom:16px"><?php echo h($success); ?></div><?php endif; ?>

    <!-- Tab Navigation -->
    <div class="dashboard-tabs">
      <button class="tab-btn<?php echo $action === 'list' ? ' active' : ''; ?>" onclick="switchTab('projects', this)">
        📋 Projects
      </button>
      <button class="tab-btn<?php echo $action === 'create' || $action === 'edit' ? ' active' : ''; ?>" onclick="switchTab('form', this)">
        <?php echo $action === 'edit' ? '✏️ Edit Project' : '➕ New Project'; ?>
      </button>
      <button class="tab-btn<?php echo $action === 'settings' ? ' active' : ''; ?>" onclick="switchTab('settings', this)">
        ⚙️ Profile Settings
      </button>
    </div>

    <!-- Projects List Tab -->
    <div id="projects" class="tab-content<?php echo $action === 'list' ? ' active' : ''; ?>">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h2 style="margin:0;font-size:20px">My Projects</h2>
        <button class="btn" onclick="switchTab('form', document.querySelectorAll('.tab-btn')[1]); document.querySelectorAll('.tab-btn')[1].classList.add('active'); document.querySelectorAll('.tab-btn')[0].classList.remove('active');">+ Add Project</button>
      </div>
      <table>
        <thead>
          <tr>
            <th style="width:22%">Title</th>
            <th>Description</th>
            <th style="width:20%">File</th>
            <th style="width:18%">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($p = mysqli_fetch_assoc($projects)): ?>
          <tr>
            <td><?php echo h($p['title']); ?></td>
            <td><?php echo nl2br(h($p['description'])); ?></td>
            <td>
              <?php if ($p['file_path']): ?>
                <a href="/MyPersonal_Porfolio/view_project.php?id=<?php echo (int)$p['id']; ?>" target="_blank" class="btn">View Work</a>
              <?php else: ?>
                <span style="color:#6b7280">—</span>
              <?php endif; ?>
            </td>
            <td class="row-actions">
              <button class="btn" onclick="editProject(<?php echo (int)$p['id']; ?>)">Edit</button>
              <form style="display:inline" method="post" onsubmit="return confirm('Delete this project?')">
                <input type="hidden" name="delete_project_id" value="<?php echo (int)$p['id']; ?>">
                <button class="btn red" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Project Form Tab -->
    <div id="form" class="tab-content<?php echo ($action === 'create' || $action === 'edit') ? ' active' : ''; ?>">
      <h2 style="margin-top:0;font-size:20px"><?php echo $action === 'edit' ? 'Edit Project' : 'Create New Project'; ?></h2>
      <div class="card">
        <form method="post" enctype="multipart/form-data">
          <?php if ($action === 'edit'): ?>
            <input type="hidden" name="project_id" value="<?php echo (int)$editingProjectId; ?>">
          <?php endif; ?>
          <div class="field">
            <label>Title *</label>
            <input name="title" value="<?php echo $editingProject ? h($editingProject['title']) : ''; ?>" required>
          </div>
          <div class="field">
            <label>Description *</label>
            <textarea name="description" rows="8" required><?php echo $editingProject ? h($editingProject['description']) : ''; ?></textarea>
          </div>
          <div class="field">
            <label>Attachment (optional)</label>
            <?php if ($editingProject && $editingProject['file_path']): ?>
              <div class="muted" style="margin-bottom:12px">Current: <a style="color:#93c5fd" target="_blank" href="<?php echo h($editingProject['file_path']); ?>">View</a></div>
            <?php endif; ?>
            <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.gif,.zip,.rar,.7z,.doc,.docx,.ppt,.pptx,.mp4">
            <div class="muted">Allowed: documents, images, archives, videos</div>
          </div>
          <div style="display:flex;gap:8px">
            <button class="btn" type="submit" name="project_submit"><?php echo $action === 'edit' ? 'Update Project' : 'Create Project'; ?></button>
            <button class="btn gray" type="button" onclick="switchTab('projects', document.querySelectorAll('.tab-btn')[0]); document.querySelectorAll('.tab-btn')[0].classList.add('active'); document.querySelectorAll('.tab-btn')[1].classList.remove('active');">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Profile Settings Tab -->
    <div id="settings" class="tab-content<?php echo $action === 'settings' ? ' active' : ''; ?>">
      <h2 style="margin-top:0;font-size:20px">Profile Settings</h2>
      <div class="card">
        <form method="post" enctype="multipart/form-data">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:16px">
            <div>
              <h3 style="margin-top:0;font-size:16px">Profile Picture</h3>
              <?php if ($currentAvatar): ?>
                <div style="margin-bottom:12px">
                  <img src="/MyPersonal_Porfolio/<?php echo h($currentAvatar); ?>" alt="Profile" style="max-width:150px;max-height:150px;border-radius:8px;border:2px solid #e5e7eb;">
                </div>
              <?php else: ?>
                <div style="margin-bottom:12px;color:#6b7280;font-size:14px">No profile picture uploaded yet</div>
              <?php endif; ?>
              <div class="field">
                <label style="font-size:14px">Upload Profile Picture</label>
                <input type="file" name="avatar" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                <div class="muted" style="font-size:12px">Will be saved as "profile.jpg" (or appropriate extension)</div>
              </div>
            </div>
            <div>
              <h3 style="margin-top:0;font-size:16px">Intro Text</h3>
              <div class="field">
                <label style="font-size:14px">Introduction Text</label>
                <textarea name="intro_text" rows="6" required style="font-size:14px"><?php echo h($currentIntro); ?></textarea>
                <div class="muted" style="font-size:12px">This text appears in the intro card on the homepage</div>
              </div>
            </div>
          </div>
          
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px">
            <div>
              <h3 style="margin-top:0;font-size:16px">Education</h3>
              <div class="field">
                <label style="font-size:14px">Education Text (one item per line for bullets)</label>
                <textarea name="education_text" rows="8" style="font-size:14px" placeholder="Camarines Sur Polytechnic Colleges&#10;Bachelor of Science in Information Technology&#10;University of Saint Anthony&#10;High School Diploma"><?php echo h($currentEducation); ?></textarea>
                <div class="muted" style="font-size:12px">Each line will be displayed as a bullet point</div>
              </div>
            </div>
            <div>
              <h3 style="margin-top:0;font-size:16px">Services</h3>
              <div class="field">
                <label style="font-size:14px">Services Text (one item per line for bullets)</label>
                <textarea name="services_text" rows="8" style="font-size:14px" placeholder="Freelance&#10;Tutoring"><?php echo h($currentServices); ?></textarea>
                <div class="muted" style="font-size:12px">Each line will be displayed as a bullet point</div>
              </div>
            </div>
          </div>
          
          <button class="btn" type="submit" name="settings_submit">Save Settings</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    function switchTab(tabId, btnElement) {
      // Hide all tabs
      document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
      });
      
      // Remove active class from all buttons
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      
      // Show selected tab
      document.getElementById(tabId).classList.add('active');
      
      // Add active class to clicked button
      btnElement.classList.add('active');
      
      // Update button text for form tab
      if (tabId === 'form') {
        btnElement.textContent = '➕ New Project';
      }
    }
    
    function editProject(projectId) {
      // Switch to form tab
      const formTab = document.querySelectorAll('.tab-btn')[1];
      switchTab('form', formTab);
      formTab.textContent = '✏️ Edit Project';
      
      // Redirect to edit mode
      window.location.href = '?action=edit&id=' + projectId;
    }
  </script>
</body>
</html>


