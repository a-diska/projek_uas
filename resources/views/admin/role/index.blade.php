@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Role</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Role</h5>
        <a href="" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Data
        </a>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-role">
                <tr>
                    <td colspan="3" class="text-center text-muted">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadRole();
});

function loadRole() {
    const token = localStorage.getItem('auth_token');
    const tbody = document.getElementById("tabel-role");

    if (!token) {
        console.error("Token tidak ditemukan.");
        tbody.innerHTML = `<tr><td colspan="3" class="text-center text-danger">Token tidak ditemukan. Harap login ulang.</td></tr>`;
        return;
    }

    fetch('/api/role', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => {
                throw new Error(err.message || 'Terjadi kesalahan');
            });
        }
        return res.json();
    })
    .then(response => {
        tbody.innerHTML = "";

        if (response.success && response.data.length > 0) {
            response.data.forEach((role, index) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${role.nama_role}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="showRole(${role.id_role})" title="Lihat">
                                <i class='bx bx-show'></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="window.location.href='/admin/role/${role.id_role}/edit'" title="Edit">
                                <i class='bx bx-edit-alt'></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="hapusRole(${role.id_role})" title="Hapus">
                                <i class='bx bx-trash'></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted">Data role belum tersedia.</td></tr>`;
        }
    })
    .catch(error => {
        console.error("Gagal mengambil data role:", error.message);
        tbody.innerHTML = `<tr><td colspan="3" class="text-center text-danger">${error.message}</td></tr>`;
    });
}

function showRole(id) {
    const token = localStorage.getItem('auth_token');

    fetch(`/api/role/${id}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            const role = response.data;
            Swal.fire({
                title: 'Detail Role',
                html: `
                    <div style="text-align:left">
                        <p><strong>ID:</strong> ${role.id_role}</p>
                        <p><strong>Nama Role:</strong> ${role.nama_role}</p>
                    </div>
                `,
                confirmButtonText: 'Tutup'
            });
        } else {
            Swal.fire('Gagal', response.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Gagal mengambil data role.', 'error');
    });
}

function hapusRole(id) {
    const token = localStorage.getItem('auth_token');

    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data role akan dihapus permanen!",
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/role/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    Swal.fire('Berhasil', 'Role dihapus!', 'success');
                    loadRole();
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Tidak dapat menghapus data.', 'error');
            });
        }
    });
}
</script>
@endsection
