    const navbar    = document.getElementById('mainNavbar');
    const hamburger = document.querySelector('.hamburger');

    /* Scroll effect */
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 20);
    });

    /* Hamburger toggle */
    hamburger.addEventListener('click', () => {
        navbar.classList.toggle('open');
        hamburger.classList.toggle('active');
    });