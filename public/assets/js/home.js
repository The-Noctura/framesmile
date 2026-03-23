        const hamburger = document.querySelector('.hamburger');
        const navbar = document.querySelector('.navbar');

        if (hamburger && navbar) {
            hamburger.addEventListener('click', () => {
                hamburger.classList.toggle('active');
                navbar.classList.toggle('open');
            });

            // Tutup menu saat klik link
            navbar.querySelectorAll('.navlink a').forEach(link => {
                link.addEventListener('click', () => {
                    hamburger.classList.remove('active');
                    navbar.classList.remove('open');
                });
            });
        }