@extends('admin.index')

@section('content')
<div class="container-fluid px-4">
    <div class="mt-4 mb-4">
        <h5 class="text-muted mb-1">
            👋 Selamat datang, <span class="text-dark fw-semibold">Admin!</span>
        </h5>
        <p class="text-muted fst-italic mb-0">
            Pantau data pengguna dan pengajuan dengan mudah di sini. 🚀
        </p>
    </div>

    <div class="row g-4">
        @php
            $cards = [
                ['label' => 'Admin', 'id' => 'totalAdmin', 'icon' => 'fas fa-user-shield', 'color' => 'danger'],
                ['label' => 'Peserta', 'id' => 'totalPeserta', 'icon' => 'fas fa-users', 'color' => 'primary'],
                ['label' => 'Pengajuan', 'id' => 'totalPengajuan', 'icon' => 'fas fa-file-alt', 'color' => 'success'],
                ['label' => 'Verifikator', 'id' => 'totalVerifikator', 'icon' => 'fas fa-user-check', 'color' => 'warning'],
            ];
        @endphp

        @foreach ($cards as $card)
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 rounded-4 bg-light h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 fw-medium">{{ $card['label'] }}</p>
                        <h2 id="{{ $card['id'] }}" class="fw-bold fade-in text-{{ $card['color'] }}">Memuat...</h2>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-{{ $card['color'] }} bg-opacity-25" style="width: 50px; height: 50px;">
                        <i class="{{ $card['icon'] }} text-{{ $card['color'] }} fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Animasi transisi -->
<style>
    .fade-in {
        transition: opacity 0.3s ease-in-out;
        opacity: 0.4;
    }
    .fade-in.loaded {
        opacity: 1;
    }
</style>

<!-- Fetch data -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        fetch('/api/dashboard')
            .then(response => response.json())
            .then(data => {
                updateCard('totalAdmin', data.totalAdmin);
                updateCard('totalPeserta', data.totalPeserta);
                updateCard('totalVerifikator', data.totalVerifikator);
                updateCard('totalPengajuan', data.totalPengajuan);
            })
            .catch(error => {
                console.error('Gagal memuat data:', error);
                ['totalAdmin', 'totalPeserta', 'totalVerifikator', 'totalPengajuan'].forEach(id => {
                    const el = document.getElementById(id);
                    el.textContent = 'Gagal';
                    el.classList.add('text-decoration-line-through');
                });
            });

        function updateCard(id, value) {
            const el = document.getElementById(id);
            el.textContent = value;
            el.classList.add('loaded');
        }
    });
</script>
@endsection
