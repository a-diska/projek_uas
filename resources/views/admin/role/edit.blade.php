@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Edit Role</h4>

<div class="card p-4">
    <form id="formEditRole">
        <div class="mb-3">
            <label for="id_role" class="form-label">ID Role</label>
            <input type="text" id="id_role" class="form-control" value="{{ $id_role }}" readonly>
        </div>

        <div class="mb-3">
            <label for="nama_role" class="form-label">Nama Role</label>
            <input type="text" id="nama_role" name="nama_role" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Perbarui</button>
        <a href="{{ route('admin.role.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = localStorage.getItem('auth_token');
    const id = document.getElementById('id_role').value;
    const namaInput = document.getElementById('nama_role');

    if (!token) {
        Swal.fire('Error', 'Token tidak ditemukan. Silakan login ulang.', 'error');
        return;
    }

    // Ambil data role
    fetch(`/api/role/${id}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(response => {
        if (response.success && response.data) {
            namaInput.value = response.data.nama_role;
        } else {
            Swal.fire('Gagal', response.message || 'Data tidak ditemukan.', 'error');
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        Swal.fire('Error', 'Gagal memuat data role.', 'error');
    });

    // Submit form
    document.getElementById('formEditRole').addEventListener('submit', function (e) {
        e.preventDefault();

        const nama_role = namaInput.value.trim();
        if (!nama_role) {
            Swal.fire('Peringatan', 'Nama role tidak boleh kosong.', 'warning');
            return;
        }

        fetch(`/api/role/${id}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ nama_role })
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Role berhasil diperbarui!',
                    timer: 1500,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    window.location.href = "{{ route('admin.role.index') }}";
                }, 1600);
            } else {
                Swal.fire('Gagal', response.message || 'Gagal memperbarui data.', 'error');
            }
        })
        .catch(error => {
            console.error('Update Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan saat memperbarui data.', 'error');
        });
    });
});
</script>

@endsection
