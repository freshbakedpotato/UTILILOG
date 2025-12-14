<?php

?>
  </main> 

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    (function(){
      const toggleBtn = document.getElementById("toggleBtn");
      const sidebar = document.getElementById("sidebar");
      const contentWrap = document.getElementById("content-wrap");
      const topbar = document.getElementById("topbar");
      const overlay = document.getElementById("overlay");

     
      function openSidebar() {
        sidebar.classList.remove('collapsed');
        if (window.innerWidth <= 992) {
          sidebar.classList.add('sidebar-visible');
          overlay.style.display = 'block';
        }
        contentWrap.classList.remove('full');
        topbar.classList.remove('full');
      }
      function closeSidebar() {
        if (window.innerWidth <= 992) {
          sidebar.classList.remove('sidebar-visible');
          overlay.style.display = 'none';
        } else {
          sidebar.classList.add('collapsed');
        }
        contentWrap.classList.add('full');
        topbar.classList.add('full');
      }

      
      if (window.innerWidth > 992) {
        sidebar.classList.remove('collapsed');
        contentWrap.classList.remove('full');
        topbar.classList.remove('full');
      } else {
        sidebar.classList.add('collapsed');
        contentWrap.classList.add('full');
        topbar.classList.add('full');
      }

     
      if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
          if (sidebar.classList.contains('sidebar-visible') || !sidebar.classList.contains('collapsed')) {
            closeSidebar();
          } else {
            openSidebar();
          }
        });
      }

      
      overlay.addEventListener('click', closeSidebar);

     
      sidebar.querySelectorAll('a').forEach(a=>{
        a.addEventListener('click', () => {
          if (window.innerWidth <= 992) closeSidebar();
        });
      });

      
      window.addEventListener('resize', () => {
        if (window.innerWidth > 992) {
          sidebar.classList.remove('collapsed');
          overlay.style.display = 'none';
          contentWrap.classList.remove('full');
          topbar.classList.remove('full');
        } else {
          sidebar.classList.add('collapsed');
          contentWrap.classList.add('full');
          topbar.classList.add('full');
          overlay.style.display = 'none';
        }
      });
    })();
  </script>
</body>
</html>
