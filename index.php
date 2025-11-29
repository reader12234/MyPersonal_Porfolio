<?php
require_once __DIR__ . '/db.php';

// Get avatar path
$avatarResult = mysqli_query($conn, "SELECT setting_value FROM profile_settings WHERE setting_key='avatar_path' LIMIT 1");
$avatarRow = mysqli_fetch_assoc($avatarResult);
$avatarPath = $avatarRow ? $avatarRow['setting_value'] : null;

// Get intro text
$introResult = mysqli_query($conn, "SELECT setting_value FROM profile_settings WHERE setting_key='intro_text' LIMIT 1");
$introRow = mysqli_fetch_assoc($introResult);
$introText = $introRow ? $introRow['setting_value'] : "I'm a fresh graduate with a degree in Information Technology from Camarines Sur Polytechnic Colleges, and I am eager to apply the skills and knowledge I've gained to contribute meaningfully to a professional team.";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Jan Russel — Portfolio</title>
  <link rel="stylesheet" href="style/index.css" />
</head>
<body>
  <div class="container">
    <section class="hero">
      <div class="avatar-wrapper">
        <div class="avatar">
          <?php if ($avatarPath): ?>
            <img src="/MyPersonal_Porfolio/<?php echo htmlspecialchars($avatarPath); ?>" alt="Profile Picture" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
          <?php else: ?>
            ??
          <?php endif; ?>
        </div>
        <div class="name-tag">Jan Russel S. Luceña</div>
      </div>

      <div class="intro-card">
        <h1>Hello, I'm Jan Russel S. Luceña</h1>
        <p><?php echo nl2br(htmlspecialchars($introText)); ?></p>
        <div class="experiences">
          <span>My Experiences:</span><br>
          <span class="exp-tag">Web Developer</span>
        </div>

        <div class="buttons">
          <a href="profile.php" class="btn profile">VIEW PROFILE</a>
        </div>
      </div>
    </section>

    <footer>
      ©2025 Jan Russel S. Luceña.
      <div style="margin-top: 10px;">
        <a href="/MyPersonal_Porfolio/admin/login.php" style="color: var(--muted); text-decoration: none; font-size: 12px;">Admin Panel</a>
      </div>
    </footer>
  </div>
</body>
</html>

