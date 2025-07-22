@extends('peserta.index')

@section('content')
<h4 class="fw-bold py-3 mb-4 text-center">Detail Workshop Keguruan</h4>

<div class="row justify-content-center" id="workshop-detail">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <!-- Judul Workshop -->
                <div class="mb-4">
                    <h3 class="text-primary fw-semibold mb-1" id="nama_workshop">-</h3>
                </div>

                <!-- Tanggal -->
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Tanggal</h6>
                    <p class="mb-0 text-dark">
                        <strong id="tanggal">Tanggal Mulai – Tanggal Selesai</strong>
                    </p>
                </div>

                <!-- Lokasi -->
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Lokasi</h6>
                    <p class="fs-6 text-dark mb-0" id="lokasi">-</p>
                </div>

                <!-- Tombol Kembali -->
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('peserta.workshop.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const id = {!! json_encode($id_workshop) !!};
        loadWorkshopDetail(id);
    });

    function getToken() {
        return localStorage.getItem('auth_token');
    }

    function loadWorkshopDetail(id) {
        const token = getToken();

        if (!token) {
            Swal.fire('Error', 'Token tidak tersedia. Silakan login ulang.', 'error');
            return;
        }

        fetch(`/api/workshop/${id}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            if (response.success && response.data) {
                const w = response.data;

                document.getElementById('nama_workshop').textContent = w.nama_workshop;
                document.getElementById('tanggal').textContent = `${w.tanggal_mulai} – ${w.tanggal_selesai}`;
                document.getElementById('lokasi').textContent = w.lokasi;
            } else {
                Swal.fire('Gagal', response.message || 'Data tidak ditemukan.', 'error');
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Error', 'Gagal memuat data workshop.', 'error');
        });
    }
</script>
@endsection
