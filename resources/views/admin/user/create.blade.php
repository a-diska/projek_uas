@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Tambah User</h4>

<div class="card">
    <div class="card-body">
        <form id="formUser">
            <div class="mb-3">
                <label for="id_role">Role</label>
                <select id="id_role" class="form-control" required>
                    <option value="">-- Pilih Role --</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="nama">Nama</label>
                <input type="text" id="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="no_telp">No. Telp</label>
                <input type="text" id="no_telp" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email">Email</label>
                <input type="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password">Password</label>
                <input type="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = localStorage.getItem('auth_token');

    // Ambil data role
    fetch('/api/role', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(res => {
        if (res.success) {
            const select = document.getElementById('id_role');
            res.data.forEach(role => {
                const option = document.createElement('option');
                option.value = role.id_role;
                option.textContent = `${role.id_role} - ${role.nama_role}`;
                select.appendChild(option);
            });
        } else {
            throw new Error(res.message || 'Gagal mengambil data role.');
        }
    })
    .catch(error => {
        console.error('Gagal memuat role:', error);
        Swal.fire('Error', 'Gagal memuat data role.', 'error');
    });

    // Submit form
    document.getElementById('formUser').addEventListener('submit', function (e) {
        e.preventDefault();

        const data = {
            nama: document.getElementById('nama').value,
            no_telp: document.getElementById('no_telp').value,
            email: document.getElementById('email').value,
            id_role: document.getElementById('id_role').value,
            password: document.getElementById('password').value
        };

        fetch('/api/user', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(async res => {
            const response = await res.json();

            if (res.ok && response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message || 'User berhasil ditambahkan.',
                    timer: 1500,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    window.location.href = '{{ route("admin.user.index") }}';
                }, 1500);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message || 'Gagal menyimpan data.'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data.', 'error');
        });
    });
});
</script>

@endsection
