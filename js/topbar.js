// Toggle sidebar visibility
document.querySelector('.menu-toggle').addEventListener('click', function () {
    document.querySelector('.sidebar').classList.toggle('hidden-sidebar');
    document.querySelector('.content').classList.toggle('hidden-sidebar');
});

// Toggle logout dropdown
document.querySelector('.topbar-right i').addEventListener('click', function () {
    const dropdown = document.querySelector('.welcome-dropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});

// Close the logout dropdown when clicking outside
window.addEventListener('click', function (event) {
    const dropdown = document.querySelector('.welcome-dropdown');
    if (!event.target.closest('.topbar-right')) {
        dropdown.style.display = 'none';
    }
});