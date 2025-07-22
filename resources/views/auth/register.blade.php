@extends('auth.index')

@section('title', 'Registrasi Akun | Workshop Keguruan')

@section('content')
    <div class="text-center mb-4">
        <div class="mb-2">
            <i class="bi bi-person-lines-fill fs-1 text-primary"></i>
        </div>
        <h3 class="fw-bold mb-1">Workshop Keguruan</h4>
    </div>

    <form id="registerForm">
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
        </div>

        <div class="mb-3">
            <label for="no_telp" class="form-label">Nomor Telepon</label>
            <input type="text" class="form-control" id="no_telp" name="no_telp" maxlength="13" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label"> Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi</label>
            <input type="password" class="form-control" id="password" name="password" required minlength="6">
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>

    <div class="text-center mt-3 text-muted">
        Already have an account? <a href="{{ route('login') }}">Login now</a>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const form = document.getElementById('registerForm');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch("/api/register", {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    setTimeout(() => {
                        window.location.href = `/verifikasi?email=${encodeURIComponent(data.email)}`;
                    }, 1500);
                } else {
                    showErrors(result.errors || {
                        error: result.message
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal menghubungi server. Silakan coba lagi.'
                });
            }
        });

        function showErrors(errors) {
            let errorMessages = '';
            for (const key in errors) {
                if (Array.isArray(errors[key])) {
                    errors[key].forEach(msg => errorMessages += `${msg}<br>`);
                } else {
                    errorMessages += `${errors[key]}<br>`;
                }
            }

            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Validasi',
                html: errorMessages
            });
        }
    </script>
@endsection
