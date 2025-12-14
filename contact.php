<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="contact.css" />
    <title>Contacts</title>
  </head>
  <body>
    
    <header>
     
    </header>

    <main class="container">
      
      <div class="left-section">
        <img src="uploads/um-logo.png" alt="UM Logo" />
        <h2>UtiliLog</h2>
        <h1>Contacts</h1>
      </div>

      <div class="right-section">
        <p><strong>University of Mindanao – Tagum City, Visayan Campus</strong></p>
        <p>🏫 Tagum City, Davao del Norte</p>
        <p>📧 Email: <a href="mailto:facilitymanager@umindanao.edu.ph">facilitymanager@umindanao.edu.ph</a></p>
        <p>📞 Phone: (0639) 123-4567</p>
        <p>👥 Developed by: Group 8</p>

        <div class="back-btn">
          <a href="login.php">←</a>
        </div>
      </div>
    </main>

    <footer>
      <p>© <?php echo date("Y"); ?> UtiliLog. All Rights Reserved.</p>
      <p>Developed for the University of Mindanao, Visayan Campus, Tagum City</p>
    </footer>
  </body>
</html>
