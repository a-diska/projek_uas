@extends('admin.index')

@section('content')
    <h4 class="fw-bold py-3 mb-4">Pengajuan</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pengajuan</h5>
            <a href="{{ route('admin.pengajuan.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Tambah Data
            </a>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama User</th>
                        <th>Workshop</th>
                        <th>Pelayanan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabel-pengajuan">
                    <tr>
                        <td colspan="6" class="text-center text-muted">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadPengajuan();
        });

        function getToken() {
            return localStorage.getItem('auth_token');
        }

        function formatStatus(status) {
            switch (status) {
                case 'diproses':
                    return 'diproses';
                case 'disetujui':
                    return 'disetujui';
                case 'ditolak':
                    return 'ditolak';
                default:
                    return '-';
            }
        }

        function loadPengajuan() {
            const token = getToken();

            if (!token) {
                Swal.fire('Error', 'Token tidak ditemukan. Silakan login ulang.', 'error');
                return;
            }

            fetch('/api/pengajuan', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(response => {
                const tbody = document.getElementById("tabel-pengajuan");
                tbody.innerHTML = "";

                if (response.success && response.data.length > 0) {
                    response.data.forEach((pengajuan, index) => {
                        const user = pengajuan.user && pengajuan.user.nama ? pengajuan.user.nama : '-';
                        const workshop = pengajuan.workshop && pengajuan.workshop.nama_workshop ? pengajuan.workshop.nama_workshop : '-';
                        const pelayanan = pengajuan.pelayanan && pengajuan.pelayanan.nama_pelayanan ? pengajuan.pelayanan.nama_pelayanan : '-';
                        const statusText = formatStatus(pengajuan.status);

                        tbody.innerHTML += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${user}</td>
                                <td>${workshop}</td>
                                <td>${pelayanan}</td>
                                <td>${statusText}</td>
                                <td>
                                    <a href="/admin/pengajuan/${pengajuan.id_pengajuan}" class="btn btn-sm btn-outline-primary" title="Show">
                                        <i class='bx bx-show'></i>
                                    </a>
                                    <a href="/admin/pengajuan/${pengajuan.id_pengajuan}/edit" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="hapusPengajuan(${pengajuan.id_pengajuan})">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-muted">Data pengajuan belum tersedia.</td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error("Gagal mengambil data pengajuan:", error);
                const tbody = document.getElementById("tabel-pengajuan");
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-danger">Gagal memuat data pengajuan.</td>
                    </tr>
                `;
            });
        }

        function hapusPengajuan(id_pengajuan) {
            const token = getToken();

            if (!token) {
                Swal.fire('Error', 'Token tidak ditemukan. Silakan login ulang.', 'error');
                return;
            }

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data pengajuan akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/api/pengajuan/${id_pengajuan}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message || 'Data berhasil dihapus.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadPengajuan();
                        } else {
                            Swal.fire('Gagal', data.message || 'Gagal menghapus data.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Gagal menghapus pengajuan:', error);
                        Swal.fire('Terjadi kesalahan', 'Tidak dapat menghapus data.', 'error');
                    });
                }
            });
        }
    </script>
@endsection
