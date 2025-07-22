@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Edit User</h4>

<div class="card p-4">
    <form id="formEditUser">
        <div class="mb-3">
            <label for="id" class="form-label">ID User</label>
            <input type="text" id="id" class="form-control" value="{{ $id }}" readonly>
        </div>

        <div class="mb-3">
            <label for="id_role" class="form-label">Role</label>
            <select class="form-select" id="id_role" required>
                <option value="">-- Pilih Role --</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" id="nama" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="no_telp" class="form-label">No. Telepon</label>
            <input type="text" id="no_telp" class="form-control" maxlength="12" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Perbarui</button>
        <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function () {
    const token = localStorage.getItem('auth_token');
    const id = document.getElementById('id').value;

    // Load data role
    try {
        const roleRes = await fetch('/api/role', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        const roleJson = await roleRes.json();

        if (roleJson.success) {
            const roleSelect = document.getElementById('id_role');
            roleJson.data.forEach(role => {
                const option = document.createElement('option');
                option.value = role.id_role;
                option.textContent = `${role.id_role} - ${role.nama_role}`;
                roleSelect.appendChild(option);
            });
        } else {
            throw new Error(roleJson.message || 'Gagal memuat data role.');
        }
    } catch (error) {
        console.error('Gagal memuat role:', error);
        Swal.fire('Error', 'Gagal memuat data role.', 'error');
        return;
    }

    // Load data user
    try {
        const userRes = await fetch(`/api/user/${id}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        const userJson = await userRes.json();

        if (userJson.success) {
            const data = userJson.data;
            document.getElementById('nama').value = data.nama;
            document.getElementById('no_telp').value = data.no_telp;
            document.getElementById('email').value = data.email;
            document.getElementById('id_role').value = data.id_role;
        } else {
            Swal.fire('Gagal', 'Data user tidak ditemukan.', 'error');
        }
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Gagal mengambil data user.', 'error');
    }

    // Submit form edit
    document.getElementById('formEditUser').addEventListener('submit', function (e) {
        e.preventDefault();

        const payload = {
            nama: document.getElementById('nama').value,
            no_telp: document.getElementById('no_telp').value,
            email: document.getElementById('email').value,
            id_role: document.getElementById('id_role').value
        };

        fetch(`/api/user/${id}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                Swal.fire({
                    title: 'Berhasil',
                    text: 'User berhasil diperbarui!',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });

                setTimeout(() => {
                    window.location.href = "{{ route('admin.user.index') }}";
                }, 1500);
            } else if (response.errors) {
                let pesan = Object.values(response.errors).flat().join('\n');
                Swal.fire('Gagal', pesan, 'error');
            } else {
                Swal.fire('Gagal', response.message || 'Gagal memperbarui data.', 'error');
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Error', 'Terjadi kesalahan saat memperbarui data.', 'error');
        });
    });
});
</script>
@endsection
