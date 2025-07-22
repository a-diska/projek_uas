@extends('auth.index')

@section('title', 'Login | Workshop Keguruan')

@section('content')
    <div class="text-center mb-4">
        <i class="bi bi-mortarboard-fill brand-icon"></i>
        <h4 class="fw-bold mt-2">Workshop Keguruan</h4>
    </div>

    <form id="loginForm">
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="email" class="form-control" id="email" placeholder="example@gmail.com" required>
            </div>
        </div>

        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="password" placeholder="********" required>
            </div>
        </div>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="showPassword">
                <label class="form-check-label" for="showPassword">Show</label>
            </div>
            <a href="{{ route('forgot-password') }}" class="text-decoration-none small">Forgot password?</a>
        </div>

        <p id="errorMsg" class="text-danger text-center mt-2"></p>

        <button type="submit" class="btn btn-primary w-100" id="loginButton">Login</button>
    </form>

    <div class="text-center mt-4 text-muted">
        Don't have an account?
        <a href="{{ route('register') }}">Sign up now</a>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('showPassword').addEventListener('change', function () {
            const passwordInput = document.getElementById('password');
            passwordInput.type = this.checked ? 'text' : 'password';
        });

        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const errorMsg = document.getElementById('errorMsg');
            const loginButton = document.getElementById('loginButton');

            errorMsg.textContent = '';
            loginButton.disabled = true;
            loginButton.textContent = 'Logging in...';

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    const token = result.data?.token;
                    const user = result.data?.user;

                    if (token && user?.role) {
                        localStorage.setItem('auth_token', token);
                        localStorage.setItem('user_role', user.role.toLowerCase());
                        localStorage.setItem('user_name', user.nama || '-');

                        Swal.fire({
                            title: 'Login Berhasil!',
                            text: `Selamat datang ${user.role} - ${user.nama || 'Pengguna'}`,
                            icon: 'success',
                            timer: 1500,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            didClose: () => {
                                redirectToDashboard(user.role.toLowerCase());
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Data tidak lengkap!',
                            text: 'Login berhasil tetapi data user tidak lengkap.',
                            icon: 'warning'
                        });
                    }
                } else {
                    Swal.fire({
                        title: 'Login Gagal!',
                        text: result.message || 'Email atau password salah.',
                        icon: 'error'
                    });
                }
            } catch (error) {
                console.error('Login error:', error);
                Swal.fire({
                    title: 'Terjadi Kesalahan!',
                    text: 'Gagal terhubung ke server.',
                    icon: 'error'
                });
            } finally {
                loginButton.disabled = false;
                loginButton.textContent = 'Login';
            }
        });

        function redirectToDashboard(role) {
            const routeMap = {
                admin: '/admin/dashboard',
                verifikator: '/verifikator/dashboard',
                peserta: '/peserta/dashboard'
            };
            window.location.href = routeMap[role] || '/login';
        }
    </script>
@endsection
