@extends('peserta.index')

@section('content')
<h4 class="fw-bold py-3 mb-4 text-center">Detail Pelayanan</h4>

<div class="row justify-content-center" id="pelayanan-detail">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <!-- Judul Pelayanan -->
                <div class="mb-4">
                    <h3 class="text-primary fw-semibold mb-1" id="nama_pelayanan">-</h3>
                </div>

                <!-- Deskripsi -->
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Deskripsi</h6>
                    <p class="mb-0 text-dark" id="deskripsi">-</p>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Status</h6>
                    <p class="mb-0" id="status">-</p>
                </div>

                <!-- Tombol Kembali -->
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('peserta.pelayanan.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const id = {!! json_encode($id_pelayanan) !!};
        loadPelayananDetail(id);
    });

    function getToken() {
        return localStorage.getItem('auth_token');
    }

    function loadPelayananDetail(id) {
        const token = getToken();

        if (!token) {
            Swal.fire('Error', 'Token tidak tersedia. Silakan login ulang.', 'error');
            return;
        }

        fetch(`/api/pelayanan/${id}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            if (response.success && response.data) {
                const p = response.data;

                document.getElementById('nama_pelayanan').textContent = p.nama_pelayanan;
                document.getElementById('deskripsi').textContent = p.deskripsi;
                document.getElementById('status').innerHTML = 
                    p.status === 'aktif'
                        ? `<span class="badge bg-success">Aktif</span>`
                        : `<span class="badge bg-secondary">Nonaktif</span>`;
            } else {
                Swal.fire('Gagal', response.message || 'Data tidak ditemukan.', 'error');
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Error', 'Gagal memuat data pelayanan.', 'error');
        });
    }
</script>
@endsection
