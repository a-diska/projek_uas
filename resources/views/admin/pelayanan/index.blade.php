@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Pelayanan</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Pelayanan</h5>
        <a href="{{ route('admin.pelayanan.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Data
        </a>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelayanan</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-pelayanan" class="table-border-bottom-0">
                <tr>
                    <td colspan="5" class="text-center text-muted">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadPelayanan();
    });

    function getToken() {
        return localStorage.getItem('auth_token');
    }

    function loadPelayanan() {
        const token = getToken();
        if (!token) {
            Swal.fire('Error', 'Token tidak ditemukan. Silakan login ulang.', 'error');
            return;
        }

        fetch('/api/pelayanan', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            const tbody = document.getElementById("tabel-pelayanan");
            tbody.innerHTML = "";

            if (response.success && response.data.length > 0) {
                response.data.forEach((item, index) => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.nama_pelayanan}</td>
                            <td>${item.deskripsi}</td>
                            <td>${item.status.toLowerCase()}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Show" onclick="showPelayanan(${item.id_pelayanan})">
                                    <i class='bx bx-show'></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Edit" onclick="window.location.href='/admin/pelayanan/${item.id_pelayanan}/edit'">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="hapusPelayanan(${item.id_pelayanan})">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted">Data pelayanan belum tersedia.</td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error("Gagal mengambil data pelayanan:", error);
            document.getElementById("tabel-pelayanan").innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger">Gagal memuat data pelayanan.</td>
                </tr>
            `;
        });
    }

    function showPelayanan(id) {
        const token = getToken();
        fetch(`/api/pelayanan/${id}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                const p = response.data;
                Swal.fire({
                    title: 'Detail Pelayanan',
                    html: `
                        <div style="text-align: left">
                            <p><strong>No:</strong> ${p.id_pelayanan}</p>
                            <p><strong>Nama Pelayanan:</strong> ${p.nama_pelayanan}</p>
                            <p><strong>Deskripsi:</strong> ${p.deskripsi}</p>
                            <p><strong>Status:</strong> ${p.status.toLowerCase()}</p>
                        </div>
                    `,
                    confirmButtonText: 'Tutup'
                });
            } else {
                Swal.fire('Gagal', response.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Gagal mengambil data pelayanan.', 'error');
        });
    }

    function hapusPelayanan(id) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data pelayanan akan dihapus secara permanen!",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                const token = getToken();
                fetch(`/api/pelayanan/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data pelayanan berhasil dihapus!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadPelayanan();
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Gagal menghapus pelayanan:', error);
                    Swal.fire('Terjadi kesalahan', 'Tidak dapat menghapus data.', 'error');
                });
            }
        });
    }
</script>
@endsection
