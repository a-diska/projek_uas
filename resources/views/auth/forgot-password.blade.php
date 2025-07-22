@extends('auth.index')

@section('title', 'Lupa Password')

@section('content')
<div class="text-center mb-4">
    <i class="bi bi-mortarboard-fill brand-icon"></i>
    <h4 class="fw-bold mt-2 text-primary">Workshop Keguruan</h4>
    <p class="text-muted">Masukkan email Anda untuk mengirim OTP</p>
</div>

<form id="forgotForm">
    <div class="mb-3">
        <label class="form-label">Email</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="email" required>
        </div>
        <div class="invalid-feedback" id="emailError"></div>
    </div>

    <button type="submit" class="btn btn-primary w-100 rounded-pill" id="submitBtn">
        <span id="btnText"><i class="bi bi-send-fill me-1"></i> Send OTP</span>
        <span id="btnLoading" class="d-none"><i class="bi bi-arrow-repeat me-1 spin"></i> Send...</span>
    </button>
</form>

<div class="text-center mt-3">
    <small><a href="{{ route('login') }}" class="text-muted">Back to login</a></small>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const form = document.getElementById('forgotForm');
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('emailError');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        emailInput.classList.remove('is-invalid');
        emailError.textContent = '';
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        submitBtn.disabled = true;

        try {
            const res = await fetch('/api/forgot-password/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: emailInput.value })
            });

            const data = await res.json();

            if (!res.ok) {
                if (data.errors?.email) {
                    emailInput.classList.add('is-invalid');
                    emailError.textContent = data.errors.email[0];
                }
                throw new Error(data.message || 'Gagal mengirim OTP.');
            }

            localStorage.setItem('reset_email', emailInput.value);

            await Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });

            window.location.href = '{{ route("verifikasi-otp") }}';

        } catch (err) {
            await Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: err.message
            });
        } finally {
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            submitBtn.disabled = false;
        }
    });
</script>

<style>
    .spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        100% { transform: rotate(360deg); }
    }
</style>
@endsection
