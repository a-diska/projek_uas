@extends('peserta.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Daftar Pelayanan</h4>

<div class="row" id="list-pelayanan">
    <div class="col-12">
        <div class="text-center text-muted">Memuat data pelayanan...</div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', loadPelayanan);

    function getToken() {
        return localStorage.getItem('auth_token');
    }

    function showLoading(container, message = 'Memuat data...') {
        container.innerHTML = `<div class="col-12 text-center text-muted">${message}</div>`;
    }

    function showError(container, message = 'Terjadi kesalahan.') {
        container.innerHTML = `<div class="col-12 text-center text-danger">${message}</div>`;
    }

    function loadPelayanan() {
        const token = getToken();
        const container = document.getElementById("list-pelayanan");

        if (!token) {
            return showError(container, 'Token tidak ditemukan. Silakan login ulang.');
        }

        showLoading(container);

        fetch('/api/pelayanan', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            container.innerHTML = "";

            if (response.success && Array.isArray(response.data) && response.data.length) {
                response.data.forEach(pelayanan => {
                    container.innerHTML += renderPelayananCard(pelayanan);
                });
            } else {
                showError(container, 'Belum ada data pelayanan tersedia.');
            }
        })
        .catch(error => {
            console.error("Gagal mengambil data pelayanan:", error);
            showError(container, 'Gagal memuat data pelayanan.');
        });
    }

    function renderPelayananCard(pelayanan) {
        const statusBadge = pelayanan.status === 'aktif'
            ? `<span class="badge bg-success"><i class='bx bx-check-circle'></i> Aktif</span>`
            : `<span class="badge bg-secondary"><i class='bx bx-block'></i> Nonaktif</span>`;

        return `
            <div class="col-md-6 col-xl-4 mb-4">
                <div class="card shadow-sm h-100 border-0 hover-shadow">
                    <div class="card-body p-4">
                        <h5 class="card-title text-primary">
                            <i class='bx bx-detail'></i> ${pelayanan.nama_pelayanan}
                        </h5>
                        <p class="text-muted mb-2">
                            <i class='bx bx-info-circle'></i> ${pelayanan.deskripsi}
                        </p>
                        <div class="mb-3">
                            ${statusBadge}
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='/peserta/pelayanan/${pelayanan.id_pelayanan}'">
                            <i class='bx bx-show'></i> Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

</script>
@endsection
