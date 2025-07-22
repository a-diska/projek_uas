@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Verifikator</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Verifikator</h5>
        <a href="{{ route('admin.verifikator.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Data
        </a>
    </div>

    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama User</th>
                    <th>Jabatan</th>
                    <th>Tahapan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-verifikator" class="table-border-bottom-0">
                <tr>
                    <td colspan="6" class="text-center text-muted">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadVerifikator();
});

function getToken() {
    return localStorage.getItem('auth_token');
}

function loadVerifikator() {
    const token = getToken();

    fetch('/api/verifikator', {
        headers: {
            'Authorization': token ? `Bearer ${token}` : '',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(response => {
        const tbody = document.getElementById("tabel-verifikator");
        tbody.innerHTML = "";

        if (response.success && Array.isArray(response.data) && response.data.length > 0) {
            response.data.forEach((item, index) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.user?.nama || '-'}</td>
                        <td>${item.jabatan || '-'}</td>
                        <td>${formatTahapan(item.tahapan)}</td>
                        <td>${item.status || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" title="Show" onclick="showVerifikator(${item.id_verifikator})">
                                <i class='bx bx-show'></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" title="Edit" onclick="window.location.href='/admin/verifikator/${item.id_verifikator}/edit'">
                                <i class="bx bx-edit-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="hapusVerifikator(${item.id_verifikator})">
                                <i class='bx bx-trash'></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted">Data verifikator belum tersedia.</td>
                </tr>
            `;
        }
    })
    .catch(error => {
        console.error("Gagal mengambil data verifikator:", error);
        document.getElementById("tabel-verifikator").innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger">Gagal memuat data verifikator.</td>
            </tr>
        `;
    });
}

function formatTahapan(tahapan) {
    if (!tahapan) return '-';
    const map = {
        1: "tahap 1",
        2: "tahap 2",
        3: "tahap 3"
    };
    return map[tahapan] || `Tahapan ${tahapan}`;
}

function showVerifikator(id) {
    const token = getToken();

    fetch(`/api/verifikator/${id}`, {
        headers: {
            'Authorization': token ? `Bearer ${token}` : '',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            const item = response.data;

            Swal.fire({
                title: 'Detail Verifikator',
                html: `
                    <div style="text-align: left">
                        <p><strong>ID:</strong> ${item.id_verifikator}</p>
                        <p><strong>Nama User:</strong> ${item.user?.nama || '-'}</p>
                        <p><strong>Jabatan:</strong> ${item.jabatan || '-'}</p>
                        <p><strong>Tahapan:</strong> ${formatTahapan(item.tahapan)}</p>
                        <p><strong>Status:</strong> ${item.status || '-'}</p>
                    </div>
                `,
                confirmButtonText: 'Tutup'
            });
        } else {
            Swal.fire('Gagal', response.message || 'Data tidak ditemukan.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Gagal mengambil data verifikator.', 'error');
    });
}

function hapusVerifikator(id) {
    const token = getToken();

    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data verifikator akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/verifikator/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': token ? `Bearer ${token}` : '',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Verifikator berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadVerifikator();
                } else {
                    Swal.fire('Gagal', data.message || 'Gagal menghapus data.', 'error');
                }
            })
            .catch(error => {
                console.error('Gagal menghapus verifikator:', error);
                Swal.fire('Terjadi kesalahan', 'Tidak dapat menghapus data.', 'error');
            });
        }
    });
}
</script>
@endsection
