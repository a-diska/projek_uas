@extends('auth.index')

@section('title', 'Verifikasi OTP | Workshop Keguruan')

@section('content')
    <div class="text-center mb-4">
        <i class="bi bi-mortarboard-fill brand-icon"></i>
        <h4 class="fw-bold mt-2 text-primary">Workshop Keguruan</h4>
        <p class="text-muted">Masukkan kode OTP yang dikirim ke email Anda</p>
    </div>

    <form id="otpForm">
        <div class="d-flex justify-content-between gap-2 mb-4 justify-content-center">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" name="otp[]" maxlength="1" class="form-control text-center fs-5 otp-input p-2"
                    style="width: 48px;" required>
            @endfor
        </div>

        <button type="submit" class="btn btn-primary w-100 rounded-pill" id="submitOtpBtn">
            <span class="default-text">Verify OTP</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>

        <div class="text-center mt-3">
            <small>Didn't receive the OTP? <a href="#" class="text-decoration-none">Resend</a></small><br>
            <a href="{{ route('login') }}" class="text-decoration-none small">Back to login</a>
        </div>
    </form>

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const otpForm = document.getElementById("otpForm");
        const otpInputs = document.querySelectorAll(".otp-input");
        const submitBtn = document.getElementById("submitOtpBtn");
        const spinner = submitBtn.querySelector(".spinner-border");
        const resendOtp = document.getElementById("resendOtp");
        const resendSpinner = document.getElementById("resendSpinner");

        otpInputs.forEach((input, i) => {
            input.addEventListener("input", () => {
                if (input.value.length === 1 && i < otpInputs.length - 1) {
                    otpInputs[i + 1].focus();
                }
            });
        });

        function toggleButtonLoading(button, spinner, isLoading) {
            button.disabled = isLoading;
            spinner.classList.toggle('d-none', !isLoading);
            button.querySelector(".default-text").classList.toggle('d-none', isLoading);
        }

        otpForm.addEventListener("submit", async function(e) {
            e.preventDefault();

            const otp = Array.from(otpInputs).map(input => input.value).join('');
            if (otp.length !== 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'OTP Tidak Lengkap',
                    text: 'Silakan isi semua 6 digit OTP.',
                });
                return;
            }

            toggleButtonLoading(submitBtn, spinner, true);

            try {
                const response = await fetch('/api/forgot-password/verifikasi-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        otp
                    })
                });

                const json = await response.json();

                if (response.ok && json.success) {
                    localStorage.setItem("reset_user_id", json.data.id_user);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: json.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '/reset-password';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Verifikasi Gagal',
                        text: json.message || "OTP tidak valid.",
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Gagal melakukan verifikasi OTP.',
                });
            } finally {
                toggleButtonLoading(submitBtn, spinner, false);
            }
        });

        resendOtp.addEventListener("click", async function(e) {
            e.preventDefault();

            const email = localStorage.getItem("reset_email");
            if (!email) {
                Swal.fire({
                    icon: 'error',
                    title: 'Email Tidak Ditemukan',
                    text: 'Silakan ulangi proses lupa password dari awal.',
                });
                return;
            }

            resendOtp.classList.add("disabled");
            resendSpinner.classList.remove("d-none");

            try {
                const response = await fetch('/api/forgot-password/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email
                    })
                });

                const json = await response.json();

                Swal.fire({
                    icon: response.ok ? 'success' : 'error',
                    title: response.ok ? 'OTP Dikirim' : 'Gagal Mengirim OTP',
                    text: json.message,
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Gagal mengirim ulang OTP.',
                });
            } finally {
                resendOtp.classList.remove("disabled");
                resendSpinner.classList.add("d-none");
            }
        });
    </script>
@endsection
