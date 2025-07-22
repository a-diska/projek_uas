@extends('auth.index')

@section('title', 'Reset Password | Workshop Keguruan')

@section('content')
<div class="text-center mb-4">
    <i class="bi bi-mortarboard-fill brand-icon"></i>
    <h4 class="fw-bold mt-2 text-primary">Workshop Keguruan</h4>
    <p class="text-muted">Silakan atur password baru Anda</p>
</div>

<form id="resetForm">
    <div class="mb-3">
        <label class="form-label">Password Baru</label>
        <input type="password" class="form-control" id="password" required minlength="6">
    </div>
    <div class="mb-3">
        <label class="form-label">Konfirmasi Password</label>
        <input type="password" class="form-control" id="password_confirmation" required minlength="6">
    </div>

    <button type="submit" class="btn btn-primary w-100 rounded-pill" id="submitResetBtn">
        <span class="default-text">Reset Password</span>
        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>
</form>

<div class="text-center mt-3">
    <small><a href="{{ route('login') }}" class="text-muted">Back to login</a></small>
</div>

{{-- Tambahkan SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const form = document.getElementById('resetForm');
    const password = document.getElementById('password');
    const password_confirmation = document.getElementById('password_confirmation');
    const btn = document.getElementById('submitResetBtn');
    const spinner = btn.querySelector('.spinner-border');
    const defaultText = btn.querySelector('.default-text');

    function toggleLoading(isLoading) {
        btn.disabled = isLoading;
        spinner.classList.toggle('d-none', !isLoading);
        defaultText.classList.toggle('d-none', isLoading);
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const id = localStorage.getItem("reset_user_id");
        if (!id) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'ID pengguna tidak ditemukan.',
            });
            return;
        }

        toggleLoading(true);

        try {
            const response = await fetch('/api/forgot-password/reset-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id,
                    password: password.value,
                    password_confirmation: password_confirmation.value
                })
            });

            const result = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: result.message,
                    confirmButtonText: 'OK',
                }).then(() => {
                    localStorage.removeItem("reset_user_id");
                    localStorage.removeItem("reset_email");
                    window.location.href = "{{ route('login') }}";
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: result.message || "Terjadi kesalahan.",
                });
            }
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "Gagal mengirim data.",
            });
        } finally {
            toggleLoading(false);
        }
    });
</script>
@endsection
