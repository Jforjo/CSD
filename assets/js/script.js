const navList = document.querySelectorAll('nav ul li');
navList.forEach((navItem, index) => {
    if (navItem.classList.contains('listening')) return;
    navItem.addEventListener('click', (e) => {
        // Prevent the a tag from redirecting
        e.preventDefault();
        // If it's already active
        if (navItem.classList.contains('active')) return;
        // Remove active class from the previous active link
        navList.forEach(navItem => {
            navItem.classList.remove('active');
            navItem.querySelector('svg')?.classList.remove('nc-int-icon-state-b');
        });
        // make this link active
        navItem.querySelector('svg')?.classList.add('nc-int-icon-state-b');
        navItem.classList.add('active');
        document.querySelector('nav .indicator')?.style.setProperty('--pos', index);
        // Load the page
        linkHandler(navItem.querySelector('a')?.href);
    });
});

function linkHandler(link) {
    return;
}