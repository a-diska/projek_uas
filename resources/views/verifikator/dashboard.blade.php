@extends('verifikator.index')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5">
            <i class="bi bi-patch-check-fill text-success mb-3" style="font-size: 4rem;"></i>
            <h2 class="fw-bold text-dark">Selamat Datang, Verifikator 👋</h2>
            <h4 class="fw-semibold text-primary mb-3">
                Halo, {{ Auth::user()->nama ?? 'Verifikator' }}!
            </h4>
            <hr class="mx-auto mt-3" style="width: 80px; border-top: 3px solid #195787;">
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <p class="text-dark fs-6">
                    Anda telah masuk sebagai verifikator dalam sistem Workshop Keguruan.
                    Tugas Anda adalah memeriksa dan memverifikasi setiap pengajuan peserta dengan teliti.
                </p>
                <p class="text-muted fst-italic mt-3">
                    Jalankan peran Anda dengan profesional, objektif, dan penuh tanggung jawab.
                </p>
            </div>
        </div>
    </div>
@endsection
