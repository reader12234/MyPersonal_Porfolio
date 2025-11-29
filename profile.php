<?php
require_once __DIR__ . '/db.php';
session_start();

// Get avatar path
$avatarResult = mysqli_query($conn, "SELECT setting_value FROM profile_settings WHERE setting_key='avatar_path' LIMIT 1");
$avatarRow = mysqli_fetch_assoc($avatarResult);
$avatarPath = $avatarRow ? $avatarRow['setting_value'] : null;

// Get Education, Services, and Contact
$educationResult = mysqli_query($conn, "SELECT setting_value FROM profile_settings WHERE setting_key='education_text' LIMIT 1");
$educationRow = mysqli_fetch_assoc($educationResult);
$educationText = $educationRow ? $educationRow['setting_value'] : '';

$servicesResult = mysqli_query($conn, "SELECT setting_value FROM profile_settings WHERE setting_key='services_text' LIMIT 1");
$servicesRow = mysqli_fetch_assoc($servicesResult);
$servicesText = $servicesRow ? $servicesRow['setting_value'] : '';

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Jan Russel — Portfolio</title>
  <link rel="stylesheet" href="style/profile.css" />
</head>
<body>
  <div class="container">
    <!-- Header -->
    <header>
      <div style="display:flex;gap:14px;align-items:center">
        <strong>Jan Russel S. Luceña</strong>
        <nav>
          <a href="#about">About</a>
          <a href="index.php">Skills</a>
          <a href="#experience">Experiences</a>
          <a href="#projects">Projects</a>
          <a href="#contact">Contact</a>
        </nav>
      </div>
      <div class="links">
        <a href="https://github.com/reader12234">GitHub</a>
        <a href="index.php">Profile Card</a>
        <a href="/MyPersonal_Porfolio/admin/login.php" title="Admin Panel" 
        style="display:inline-flex;align-items:center;justify-content:center;width:24px;
        height:24px;color: var(--muted); text-decoration: none;">

          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
          stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
          </svg>
        </a>
      </div>
    </header>

    <!-- Hero -->
    <section class="hero">
      <div class="avatar">
        <?php if ($avatarPath): ?>
          <img src="/MyPersonal_Porfolio/<?php echo htmlspecialchars($avatarPath); ?>" 
          alt="Profile Picture" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
        <?php else: ?>
          ??
        <?php endif; ?>
      </div>
      <div class="details">
        <h1>Jan Russel S. Luceña</h1>
        <p class="contact-info">
          <strong>Phone:</strong> 63496729253523 • 
          <strong>Email:</strong> <a href="mailto:[email protected]">[email protected]</a> • 
          Masoli, Bato, Camarines Sur
        </p>
      </div>
    </section>

    <!-- Main Content -->
    <div class="two-col">
      <main>
        <section id="about" class="section">
          <h2>About</h2>
          <p>A highly motivated and results-driven Information Technology graduate from 
            Camarines Sur Polytechnic Colleges. 
            Eager to leverage foundational knowledge in modern web development to contribute to 
            innovative projects. I am proficient in web 
            development committed to continuous learning, and seeking an opportunity to grow as a 
            Senior Web Developer / IT Support Specialist 
            in a dynamic environment</p>
        </section>

        <section id="skills" class="section skills">
          <h2>Skills</h2>
          <div class="skill">
            <div class="label"><span>NodeJS</span><span>10%</span></div>
            <div class="progress"><i style="width:10%"></i></div>
          </div>
          <div class="skill">
            <div class="label"><span>PHP</span><span>20%</span></div>
            <div class="progress"><i style="width:20%"></i></div>
          </div>
          <div class="skill">
            <div class="label"><span>JavaScript</span><span>15%</span></div>
            <div class="progress"><i style="width:15%"></i></div>
          </div>
          <div class="skill">
            <div class="label"><span>MySQL</span><span>20%</span></div>
            <div class="progress"><i style="width:20%"></i></div>
          </div>
        </section>
        <!-- Dynamic Projects (text only) -->
        <section id="projects" class="section">
          <h2>Projects</h2>
          <div class="project-list">
            <?php
            $result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
            while ($row = $result->fetch_assoc()):
            ?>
              <div class="project-item">
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <?php if ($row['file_path']): ?>
                  <a href="view_project.php?id=<?= (int)$row['id'] ?>" class="btn">View Work</a>
                <?php endif; ?>
              </div>
            <?php endwhile; ?>
          </div>
        </section>
      </main>

      <!-- Sidebar -->
      <aside>
        <section class="section">
          <h3>Education</h3>
          <?php if ($educationText): ?>
            <ul>
              <?php 
              $educationLines = array_filter(array_map('trim', explode("\n", $educationText)));
              foreach ($educationLines as $line): 
                if (!empty($line)):
              ?>
                <li><?php echo htmlspecialchars($line); ?></li>
              <?php 
                endif;
              endforeach; 
              ?>
            </ul>
          <?php else: ?>
            <ul>
              <li>Camarines Sur Polytechnic Colleges - Bachelor of Science in Information Technology</li>
              <li>University of Saint Anthony - High School Diploma</li>
            </ul>
          <?php endif; ?>
        </section>

        <section class="section">
          <h3>Services</h3>
          <?php if ($servicesText): ?>
            <ul>
              <?php 
              $servicesLines = array_filter(array_map('trim', explode("\n", $servicesText)));
              foreach ($servicesLines as $line): 
                if (!empty($line)):
              ?>
                <li><?php echo htmlspecialchars($line); ?></li>
              <?php 
                endif;
              endforeach; 
              ?>
            </ul>
          <?php else: ?>
            <ul>
              <li>Freelance</li>
              <li>Tutoring</li>
            </ul>
          <?php endif; ?>
        </section>

        <section id="contact" class="section contact">
          <h3>Contact</h3>
          <form>
            <input type="text" name="name" placeholder="Your name" required>
            <input type="email" name="email" placeholder="Your email" required>
            <textarea name="message" rows="4" placeholder="Message" required></textarea>
            <button type="submit">Send Message</button>
          </form>
        </section>
      </aside>
    </div>

    <!-- Footer -->
    <footer>
      ©2025 Jan Russel S. Luceña.
    </footer>
  </div>
</body>
</html>
