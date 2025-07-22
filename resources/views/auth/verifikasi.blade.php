@extends('auth.index')

@section('title', 'Verifikasi OTP')

@section('content')
    <div class="text-center mb-4">
        <div class="mb-3">
            <i class="bi bi-envelope-check-fill text-primary fs-1"></i>
        </div>
        <h4 class="fw-bold">Verifikasi Akun </h4>
        <p class="text-muted mb-0">Masukkan kode OTP yang telah dikirim ke email Anda.</p>
    </div>

    <form id="otpForm">
        <div class="d-flex justify-content-between mb-4 otp-box">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" class="form-control text-center otp-input" name="otp[]" required
                    style="width: 45px; font-size: 1.5rem;">
            @endfor
        </div>

        <button type="submit" class="btn btn-primary w-100">Verify</button>
    </form>

    <div class="text-center mt-3 text-muted" id="resendBox">
        Resend OTP in <span id="timer">10:00</span><br>
        <button class="btn btn-link p-0 mt-1" id="resendBtn" style="display: none;">Request code again</button>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        inputs[0].focus();

        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === "Backspace" && input.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        let timeLeft = 600;
        const timerEl = document.getElementById('timer');
        const resendBtn = document.getElementById('resendBtn');
        let countdownInterval = null;

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerEl.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        function startCountdown() {
            updateTimer();
            countdownInterval = setInterval(() => {
                timeLeft--;
                updateTimer();

                if (timeLeft < 0) {
                    clearInterval(countdownInterval);
                    timerEl.textContent = '00:00';
                    resendBtn.style.display = 'inline';
                }
            }, 1000);
        }

        startCountdown();

        resendBtn.addEventListener('click', async () => {
            Swal.fire({
                icon: 'info',
                title: 'OTP baru dikirim',
                text: 'Kode OTP baru telah dikirim ke email Anda.',
                timer: 2500,
                showConfirmButton: false
            });

            resendBtn.style.display = 'none';
            timeLeft = 600;
            clearInterval(countdownInterval);
            startCountdown();

            // TODO: Optional – kirim ulang OTP dari server
            // await fetch('/api/kirim-ulang-otp?email=...');
        });

        document.getElementById('otpForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const otpCode = Array.from(inputs).map(i => i.value).join('');

            if (otpCode.length !== 6) {
                return Swal.fire({
                    icon: 'warning',
                    title: 'OTP tidak lengkap',
                    text: 'Silakan isi semua digit OTP'
                });
            }

            try {
                const response = await fetch('/api/verifikasi', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        otp: otpCode
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message || 'OTP berhasil diverifikasi.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => window.location.href = '/login');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal verifikasi',
                        text: result.message || 'OTP salah atau kedaluwarsa.'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Server',
                    text: 'Tidak dapat menghubungi server.'
                });
            }
        });
    </script>
@endsection
