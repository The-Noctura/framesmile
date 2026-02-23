document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.Sign-up-form');

    form.addEventListener('submit', function(e) {
        const firstName = document.getElementById('nama-depan').value.trim();
        const lastName = document.getElementById('nama-belakang').value.trim();
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        const email = document.getElementById('email').value.trim();

        if (!firstName || !lastName || !username || !password || !email) {
            e.preventDefault();
            alert('Semua field harus diisi!');
            return;
        }

        if (username.length < 3) {
            e.preventDefault();
            alert('Username minimal 3 karakter!');
            return;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('Password minimal 8 karakter!');
            return;
        }
    });
});