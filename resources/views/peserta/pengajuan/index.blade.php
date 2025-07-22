@extends('peserta.index') 

@section('content')
<h4 class="fw-bold py-3 mb-4 d-flex justify-content-between align-items-center">
    Daftar Pengajuan
    <a href="{{ route('peserta.pengajuan.create') }}" class="btn btn-primary btn-sm">
        <i class="bx bx-plus"></i> Tambah pengajuan
    </a>
</h4>

<div class="row" id="list-pengajuan">
    <div class="col-12">
        <div class="text-center text-muted">Memuat data pengajuan...</div>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', loadPengajuan);

    function getToken() {
        return localStorage.getItem('auth_token');
    }

    function showLoading(container, message = 'Memuat data...') {
        container.innerHTML = `<div class="col-12 text-center text-muted">${message}</div>`;
    }

    function showError(container, message = 'Terjadi kesalahan.') {
        container.innerHTML = `<div class="col-12 text-center text-danger">${message}</div>`;
    }

    function loadPengajuan() {
        const token = getToken();
        const container = document.getElementById("list-pengajuan");

        if (!token) {
            return showError(container, 'Token tidak ditemukan. Silakan login ulang.');
        }

        showLoading(container);

        fetch('/api/pengajuan', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            container.innerHTML = "";

            if (response.success && Array.isArray(response.data) && response.data.length) {
                response.data.forEach(pengajuan => {
                    container.innerHTML += renderPengajuanCard(pengajuan);
                });
            } else {
                showError(container, 'Belum ada data pengajuan tersedia.');
            }
        })
        .catch(error => {
            console.error("Gagal mengambil data pengajuan:", error);
            showError(container, 'Gagal memuat data pengajuan.');
        });
    }

    function renderPengajuanCard(pengajuan) {
        const user = pengajuan.user?.nama || '-';
        const workshop = pengajuan.workshop?.nama_workshop || '-';
        const pelayanan = pengajuan.pelayanan?.nama_pelayanan || '-';

        let statusBadge = '';
        switch (pengajuan.status) {
            case 'diproses':
                statusBadge = `<span class="badge bg-warning text-dark"><i class='bx bx-time'></i> diproses</span>`; break;
            case 'disetujui':
                statusBadge = `<span class="badge bg-success"><i class='bx bx-check-circle'></i> disetujui</span>`; break;
            case 'ditolak':
                statusBadge = `<span class="badge bg-danger"><i class='bx bx-x-circle'></i> ditolak</span>`; break;
            default:
                statusBadge = `<span class="badge bg-secondary">-</span>`;
        }

        return `
            <div class="col-md-6 col-xl-4 mb-4">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title text-primary"><i class="bx bx-user"></i> ${user}</h5>
                        <p class="mb-1"><i class="bx bx-book"></i> ${workshop}</p>
                        <p class="mb-1"><i class="bx bx-briefcase"></i> ${pelayanan}</p>
                        <p>${statusBadge}</p>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button class="btn btn-sm btn-outline-primary" title="Lihat Detail" onclick="window.location.href='/peserta/pengajuan/${pengajuan.id_pengajuan}'">
                                <i class='bx bx-show'></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="Hapus" onclick="hapusPengajuan(${pengajuan.id_pengajuan})">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function hapusPengajuan(id) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data pengajuan yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/api/pengajuan/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${getToken()}`,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Pengajuan berhasil dihapus.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadPengajuan();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Gagal menghapus pengajuan.'
                        });
                    }
                })
                .catch(err => {
                    console.error('Error saat menghapus:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan',
                        text: 'Tidak dapat menghapus data.'
                    });
                });
            }
        });
    }
</script>
@endsection
