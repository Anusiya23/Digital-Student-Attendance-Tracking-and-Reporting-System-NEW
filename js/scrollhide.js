
    let lastScrollTop = 0;
    const header = document.getElementById('collegeHeader');
    const topbar = document.getElementById('topbar');
    const sidebar = document.getElementById('sidebar');

    window.addEventListener('scroll', () => {
      let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

      if (scrollTop > lastScrollTop && scrollTop > 100) {
        // Scrolling down
        header.style.top = '-200px';
        topbar.style.top = '0px';
        sidebar.style.top = '70px';
      } else {
        // Scrolling up
        header.style.top = '0';
        topbar.style.top = '160px';
        sidebar.style.top = '230px';
      }

      lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // For mobile or negative scrolling
    });
