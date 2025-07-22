@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">User</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar User</h5>
        <a href="{{ route('admin.user.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Data
        </a>
    </div>

    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Role</th>
                    <th>Nama</th>
                    <th>No. Telepon</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-user" class="table-border-bottom-0">
                <tr>
                    <td colspan="6" class="text-center text-muted">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Load Interceptor.js --}}
<script src="/js/interceptor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadUser();
});

function getToken() {
    return localStorage.getItem('auth_token');
}

function loadUser() {
    fetch('/api/user', {
        headers: {
            'Authorization': `Bearer ${getToken()}`,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(response => {
        const tbody = document.getElementById("tabel-user");
        tbody.innerHTML = "";

        if (response.success && response.data.length > 0) {
            response.data.forEach((user, index) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${user.role?.nama_role || '-'}</td>
                        <td>${user.nama}</td>
                        <td>${user.no_telp}</td>
                        <td>${user.email}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" title="Show" onclick="showUser(${user.id})">
                                <i class='bx bx-show'></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" title="Edit" onclick="window.location.href='/admin/user/${user.id}/edit'">
                                <i class="bx bx-edit-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="hapusUser(${user.id})">
                                <i class='bx bx-trash'></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted">Data user belum tersedia.</td>
                </tr>
            `;
        }
    })
    .catch(error => {
        console.error("Gagal mengambil data user:", error);
        document.getElementById("tabel-user").innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger">Gagal memuat data user.</td>
            </tr>
        `;
    });
}

function showUser(id) {
    fetch(`/api/user/${id}`, {
        headers: {
            'Authorization': `Bearer ${getToken()}`,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            const user = response.data;
            Swal.fire({
                title: 'Detail User',
                html: `
                    <div style="text-align: left">
                        <p><strong>Role:</strong> ${user.role?.nama_role || '-'}</p>
                        <p><strong>Nama:</strong> ${user.nama}</p>
                        <p><strong>No. Telepon:</strong> ${user.no_telp}</p>
                        <p><strong>Email:</strong> ${user.email}</p>
                    </div>
                `,
                confirmButtonText: 'Tutup'
            });
        } else {
            Swal.fire('Gagal', response.message || 'Gagal mengambil data user.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Gagal mengambil data user.', 'error');
    });
}

function hapusUser(id) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data user akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/user/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${getToken()}`,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'User berhasil dihapus!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadUser();
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Gagal menghapus user:', error);
                Swal.fire('Terjadi kesalahan', 'Tidak dapat menghapus data.', 'error');
            });
        }
    });
}
</script>
@endsection
