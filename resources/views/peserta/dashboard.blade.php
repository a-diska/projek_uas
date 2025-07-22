@extends('peserta.index')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Selamat Datang</h2>
            <p class="text-muted fs-5">
                di Dashboard Peserta <br>
                <span class="text-primary fw-semibold">Workshop Keguruan 2025</span>
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <h5 class="fw-semibold mb-3">Halo, {{ Auth::user()->nama ?? 'Peserta' }} 👋</h5>
                <p class="text-muted">
                    Anda telah berhasil masuk ke sistem workshop. Di sini Anda dapat mengajukan pendaftaran, mengunggah dokumen, dan memantau status pengajuan Anda.
                </p>
            </div>
        </div>
    </div>
@endsection
