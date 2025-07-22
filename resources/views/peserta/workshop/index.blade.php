@extends('peserta.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Daftar Workshop Keguruan</h4>

<div class="row" id="list-workshop">
    <div class="col-12">
        <div class="text-center text-muted">Memuat data workshop...</div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', loadWorkshop);

    function getToken() {
        return localStorage.getItem('auth_token'); // Sesuaikan dengan auth.js
    }

    function showLoading(container, message = 'Memuat...') {
        container.innerHTML = `
            <div class="col-12 text-center text-muted">${message}</div>
        `;
    }

    function showError(container, message = 'Terjadi kesalahan.') {
        container.innerHTML = `
            <div class="col-12 text-center text-danger">${message}</div>
        `;
    }

    function loadWorkshop() {
        const token = getToken();
        const container = document.getElementById("list-workshop");

        if (!token) {
            return showError(container, 'Token tidak ditemukan. Silakan login ulang.');
        }

        showLoading(container);

        fetch('/api/workshop', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            container.innerHTML = "";

            if (response.success && Array.isArray(response.data) && response.data.length) {
                response.data.forEach(workshop => {
                    container.innerHTML += renderWorkshopCard(workshop);
                });
            } else {
                showError(container, 'Belum ada workshop tersedia.');
            }
        })
        .catch(error => {
            console.error("Gagal mengambil data workshop:", error);
            showError(container, 'Gagal memuat data workshop.');
        });
    }

    function renderWorkshopCard(workshop) {
        return `
            <div class="col-md-6 col-xl-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary">${workshop.nama_workshop}</h5>
                        <p class="mb-1">
                            <i class='bx bx-calendar'></i>
                            <span class="badge bg-light text-dark">${workshop.tanggal_mulai} → ${workshop.tanggal_selesai}</span>
                        </p>
                        <p><i class='bx bx-map'></i> ${workshop.lokasi}</p>
                        <div class="d-grid">
                            <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='/peserta/workshop/${workshop.id_workshop}'">
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
