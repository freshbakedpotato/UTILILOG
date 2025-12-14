<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="about.css" />
    <title>About - UtiliLog</title>
  </head>
  <body>
    
    <header>
   
    </header>

    <main class="main-layout">
    
      <section class="logo-title">
        <img src="uploads/um-logo.png" alt="UM Logo" />
        <h1>UtiliLog</h1>
        <h2>About<br>UtiliLog</h2>
      </section>

      <section class="about-box">
        <p>
          UtiliLog is a web-based task logging and monitoring system developed for
          the utility workers of the University of Mindanao, Visayan Campus. The
          system replaces manual reporting with a digital platform that allows
          workers to record tasks, upload photo proof, and report problems in real
          time.
        </p>
        <p>
          The main objective of UtiliLog is to reduce manual paperwork, shorten
          reporting time, and provide facility managers with accurate records of
          who completed the task, where it was done, and when it was finished. It
          also includes gamification features such as leaderboards that track the
          number of tasks completed and highlight top-performing workers on a
          daily or weekly basis.
        </p>
      </section>
    </main>

    <div class="back-btn">
      <a href="login.php">←</a>
    </div>

    <footer>
      <p>© <?php echo date("Y"); ?> UtiliLog. All Rights Reserved.</p>
      <p>Developed for the University of Mindanao, Visayan Campus, Tagum City</p>
    </footer>
  </body>
</html>
