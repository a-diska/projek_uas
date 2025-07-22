document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('loginForm');
    const errorMsg = document.getElementById('errorMsg');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const res = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const result = await res.json();

            if (res.ok && result.success) {
                const token = result.data.token;
                const role = result.data.user.role.toLowerCase();
                const nama = result.data.user.nama;

                localStorage.setItem('auth_token', token);
                localStorage.setItem('user_role', role);
                localStorage.setItem('user_name', nama);

                redirectToDashboard(role);
            } else {
                errorMsg.textContent = result.message || 'Login gagal.';
            }
        } catch (err) {
            errorMsg.textContent = 'Terjadi kesalahan koneksi.';
        }
    });

    function redirectToDashboard(role) {
        const routes = {
            admin: '/admin/dashboard',
            verifikator: '/verifikator/dashboard',
            peserta: '/peserta/dashboard'
        };
        window.location.href = routes[role] || '/login';
    }
});
