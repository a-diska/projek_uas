@extends('verifikator.index')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Verifikasi Pengajuan</h4>
            <p class="text-muted mb-0">Kelola dan proses semua pengajuan yang masuk</p>
        </div>
        <div class="w-auto">
            <select id="filterStatus" class="form-select border-1 shadow-sm">
                <option value="">📁 Semua Status</option>
                <option value="diproses">⏳ Diproses</option>
                <option value="disetujui">✅ Disetujui</option>
                <option value="ditolak">❌ Ditolak</option>
            </select>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle text-center">
                    <thead class="table-light rounded-top">
                        <tr>
                            <th>No</th>
                            <th>Nama Pengguna</th>
                            <th>Workshop</th>
                            <th>Pelayanan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="7" class="text-muted text-center py-4">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .badge-status {
        padding: 0.4em 0.8em;
        font-size: 0.85rem;
        border-radius: 999px;
        font-weight: 500;
        text-transform: capitalize;
    }

    .badge-warning {
        background-color: #fff3cd;
        color: #856404;
    }

    .badge-success {
        background-color: #d4edda;
        color: #155724;
    }

    .badge-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .badge-secondary {
        background-color: #e2e3e5;
        color: #6c757d;
    }

    .btn-action {
        font-size: 0.85rem;
        padding: 0.35rem 0.6rem;
    }

    .btn-action i {
        margin-right: 4px;
    }

    .btn-info:hover {
        background-color: #0dcaf0 !important;
        color: white !important;
    }
</style>

<script>
    const token = localStorage.getItem('auth_token');

    async function loadPengajuan(status = '') {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = `<tr><td colspan="7" class="text-muted text-center py-4">Memuat data...</td></tr>`;

        const url = status ? `/api/approval?status=${status}` : `/api/approval`;

        try {
            const response = await fetch(url, {
                headers: {
                    Authorization: token
                }
            });

            const result = await response.json();

            if (!result.success || !result.data.length) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-muted text-center py-4">Tidak ada data ditemukan.</td></tr>`;
                return;
            }

            tbody.innerHTML = '';
            result.data.forEach((item, index) => {
                tbody.innerHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.user?.nama || '-'}</td>
                    <td>${item.workshop?.nama_workshop || '-'}</td>
                    <td>${item.pelayanan?.nama_pelayanan || '-'}</td>
                    <td>${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                    <td><span class="badge-status ${getBadgeClass(item.status)}">${item.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-info btn-action" onclick="window.location.href='/verifikator/pengajuan/${item.id_pengajuan}/show'">
                            <i class="bx bx-show"></i> Lihat
                        </button>
                    </td>
                </tr>
            `;
            });
        } catch (err) {
            console.error(err);
            tbody.innerHTML = `<tr><td colspan="7" class="text-danger text-center py-4">Gagal memuat data.</td></tr>`;
        }
    }

    function getBadgeClass(status) {
        switch (status) {
            case 'diproses':
                return 'badge-warning';
            case 'disetujui':
                return 'badge-success';
            case 'ditolak':
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const filter = document.getElementById('filterStatus');
        loadPengajuan();

        filter.addEventListener('change', function () {
            loadPengajuan(this.value);
        });
    });
</script>
@endsection
