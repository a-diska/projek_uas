@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Edit Pelayanan</h4>

<div class="card p-4">
    <form id="formEditPelayanan">
        <div class="mb-3">
            <label for="id_pelayanan" class="form-label">ID Pelayanan</label>
            <input type="text" id="id_pelayanan" class="form-control" value="{{ $id_pelayanan }}" readonly>
        </div>

        <div class="mb-3">
            <label for="nama_pelayanan" class="form-label">Nama Pelayanan</label>
            <input type="text" id="nama_pelayanan" name="nama_pelayanan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select" required>
                <option value="">-- Pilih Status --</option>
                <option value="aktif">aktif</option>
                <option value="nonaktif">nonaktif</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Perbarui</button>
        <a href="{{ route('admin.pelayanan.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const token = localStorage.getItem('auth_token');
        const id = document.getElementById('id_pelayanan').value;

        if (!token) {
            Swal.fire('Error', 'Token tidak ditemukan. Silakan login ulang.', 'error');
            return;
        }

        // Ambil data pelayanan
        fetch(`/api/pelayanan/${id}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            if (response.success && response.data) {
                const data = response.data;
                document.getElementById('nama_pelayanan').value = data.nama_pelayanan || '';
                document.getElementById('deskripsi').value = data.deskripsi || '';
                document.getElementById('status').value = data.status || '';
            } else {
                Swal.fire('Gagal', response.message || 'Data pelayanan tidak ditemukan.', 'error');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            Swal.fire('Error', 'Gagal mengambil data pelayanan.', 'error');
        });

        // Simpan perubahan
        document.getElementById('formEditPelayanan').addEventListener('submit', function (e) {
            e.preventDefault();

            const payload = {
                nama_pelayanan: document.getElementById('nama_pelayanan').value.trim(),
                deskripsi: document.getElementById('deskripsi').value.trim(),
                status: document.getElementById('status').value
            };

            fetch(`/api/pelayanan/${id}`, {
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
                        text: 'Pelayanan berhasil diperbarui!',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });

                    setTimeout(() => {
                        window.location.href = "{{ route('admin.pelayanan.index') }}";
                    }, 1500);
                } else {
                    Swal.fire('Gagal', response.message || 'Gagal memperbarui data.', 'error');
                }
            })
            .catch(error => {
                console.error('Submit error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat memperbarui data.', 'error');
            });
        });
    });
</script>

@endsection
