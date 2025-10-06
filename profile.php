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
          <a href="#skills">Skills</a>
          <a href="#experience">Experiences</a>
          <a href="#projects">Projects</a>
          <a href="#contact">Contact</a>
        </nav>
      </div>
      <div class="links">
        <a href="#">GitHub</a>
        <a href="#">Twitter</a>
        <a href="#">LinkedIn</a>
      </div>
    </header>

    <!-- Hero -->
    <section class="hero">
      <div class="avatar">??</div>
      <div class="details">
        <h1>Jan Russel S. Luceña</h1>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequuntur odit corrupti magnam aspernatur ex sit, dolores, eum optio reiciendis laboriosam repellat nisi dolorem provident nemo porro! Tempore et praesentium excepturi.</p>
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
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nesciunt consequatur adipisci perferendis? Harum facilis possimus commodi consectetur tempore cum, quisquam nostrum, aperiam, expedita quod explicabo omnis architecto unde? Reprehenderit, tenetur.</p>
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

        <section id="experience" class="section">
          <h2>Experiences</h2>
          <ul class="timeline">
            <li></li>
          </ul>
        </section>

        <!-- Dynamic Projects (text only) -->
        <section id="projects" class="section">
          <h2>Projects</h2>
          <div class="project-list">
            <?php
            include 'db.php';
            $result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
            while ($row = $result->fetch_assoc()):
            ?>
              <div class="project-item">
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                <?php if ($row['file_path']): ?>
                  <a href="<?= $row['file_path'] ?>" target="_blank" class="btn">View Work</a>
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
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus magnam harum quod odit voluptate sint neque, repudiandae expedita aut quia non nesciunt et rem ab qui, ex illo dolor. Quidem!</p>
        </section>

        <section class="section">
          <h3>Services</h3>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus magnam harum quod odit voluptate sint neque, repudiandae expedita aut quia non nesciunt et rem ab qui, ex illo dolor. Quidem!</p>
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
      ©2025 Jan Russel S. Luceña — Designed in plain HTML/CSS.
    </footer>
  </div>
</body>
</html>
