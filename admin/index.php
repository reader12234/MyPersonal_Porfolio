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
            // Rename to "profile" with appropriate extension
            $safeName = 'profile.' . strtolower($ext);
            $dest = $uploadDir . $safeName;
            
            // Backup old profile image if exists (rename instead of delete)
            if ($currentAvatar && file_exists(__DIR__ . '/../' . $currentAvatar)) {
                $oldPath = __DIR__ . '/../' . $currentAvatar;
                $oldExt = pathinfo($currentAvatar, PATHINFO_EXTENSION);
                $backupName = 'profile_backup_' . date('YmdHis') . '.' . $oldExt;
                $backupPath = $uploadDir . $backupName;
                // Only backup if it's not already a backup file
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
    
    // Handle intro text update
    if (isset($_POST['intro_text'])) {
        $introText = trim($_POST['intro_text']);
        $introEsc = mysqli_real_escape_string($conn, $introText);
        mysqli_query($conn, "INSERT INTO profile_settings (setting_key, setting_value) VALUES ('intro_text', '{$introEsc}') ON DUPLICATE KEY UPDATE setting_value='{$introEsc}'");
        $currentIntro = $introText;
        if ($success == '') {
            $success = 'Intro text updated successfully!';
        }
    }
    
    // Handle education text update
    if (isset($_POST['education_text'])) {
        $educationText = trim($_POST['education_text']);
        $educationEsc = mysqli_real_escape_string($conn, $educationText);
        mysqli_query($conn, "INSERT INTO profile_settings (setting_key, setting_value) VALUES ('education_text', '{$educationEsc}') ON DUPLICATE KEY UPDATE setting_value='{$educationEsc}'");
        $currentEducation = $educationText;
        if ($success == '') {
            $success = 'Settings updated successfully!';
        }
    }
    
    // Handle services text update
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

    <?php if ($error): ?><div class="error" style="margin-bottom:16px"><?php echo h($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div style="background:#10b981;color:#fff;padding:12px;border-radius:6px;margin-bottom:16px"><?php echo h($success); ?></div><?php endif; ?>

    <!-- Profile Settings Section -->
    <div class="card" style="margin-bottom:24px">
      <h2 style="margin-top:0;font-size:18px">Profile Settings</h2>
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
              <a href="/MyPersonal_Porfolio/view_project.php?id=<?php echo (int)$p['id']; ?>" target="_blank" class="btn">View Work</a>
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


