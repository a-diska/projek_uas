@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Workshop</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Workshop Keguruan</h5>
        <a href="{{ route('admin.workshop.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Data
        </a>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Workshop</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-workshop" class="table-border-bottom-0">
                <tr>
                    <td colspan="6" class="text-center text-muted">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadWorkshop();
    });

    function getToken() {
        return localStorage.getItem('auth_token'); // ✅ Gunakan auth_token
    }

    function loadWorkshop() {
        const token = getToken();
        if (!token) {
            Swal.fire('Error', 'Token tidak ditemukan. Silakan login ulang.', 'error');
            return;
        }

        fetch('/api/workshop', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            const tbody = document.getElementById("tabel-workshop");
            tbody.innerHTML = "";

            if (response.success && response.data.length > 0) {
                response.data.forEach((workshop, index) => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${workshop.nama_workshop}</td>
                            <td>${workshop.tanggal_mulai}</td>
                            <td>${workshop.tanggal_selesai}</td>
                            <td>${workshop.lokasi}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Show" onclick="showWorkshop(${workshop.id_workshop})">
                                    <i class='bx bx-show'></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Edit" onclick="window.location.href='/admin/workshop/${workshop.id_workshop}/edit'">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="hapusWorkshop(${workshop.id_workshop})">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted">Data workshop belum tersedia.</td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error("Gagal mengambil data workshop:", error);
            document.getElementById("tabel-workshop").innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">Gagal memuat data workshop.</td>
                </tr>
            `;
        });
    }

    function showWorkshop(id_workshop) {
        const token = getToken();
        fetch(`/api/workshop/${id_workshop}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                const w = response.data;
                Swal.fire({
                    title: 'Detail Workshop',
                    html: `
                        <div style="text-align: left">
                            <p><strong>No:</strong> ${w.id_workshop}</p>
                            <p><strong>Nama Workshop:</strong> ${w.nama_workshop}</p>
                            <p><strong>Tanggal Mulai:</strong> ${w.tanggal_mulai}</p>
                            <p><strong>Tanggal Selesai:</strong> ${w.tanggal_selesai}</p>
                            <p><strong>Lokasi:</strong> ${w.lokasi}</p>
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
            Swal.fire('Error', 'Gagal mengambil data workshop.', 'error');
        });
    }

    function hapusWorkshop(id_workshop) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data workshop akan dihapus secara permanen!",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                const token = getToken();
                fetch(`/api/workshop/${id_workshop}`, {
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
                            title: 'Data workshop berhasil dihapus!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadWorkshop();
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Gagal menghapus workshop:', error);
                    Swal.fire('Terjadi kesalahan', 'Tidak dapat menghapus data.', 'error');
                });
            }
        });
    }
</script>
@endsection
