// sidebar.js

// Add event listener to all sidebar links
const links = document.querySelectorAll('.sidebar-link');
links.forEach(link => {
    link.addEventListener('click', function(event) {
        // Remove 'active' class from all items
        links.forEach(item => item.parentElement.classList.remove('active'));

        // Add 'active' class to the clicked item
        this.parentElement.classList.add('active');
    });
});

// To keep the selected item active even after page reload
const currentPage = window.location.pathname.split('/').pop();
links.forEach(link => {
    if (link.getAttribute('href').includes(currentPage)) {
        link.parentElement.classList.add('active');
    }
});

