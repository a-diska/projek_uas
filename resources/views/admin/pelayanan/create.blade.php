@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Tambah Pelayanan</h4>

<div class="card">
    <div class="card-body">
        <form id="formPelayanan">
            <div class="mb-3">
                <label for="nama_pelayanan" class="form-label">Nama Pelayanan</label>
                <input type="text" class="form-control" id="nama_pelayanan" name="nama_pelayanan" required>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="aktif" selected>aktif</option>
                    <option value="nonaktif">nonaktif</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.pelayanan.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<script>
    function getToken() {
        return localStorage.getItem('auth_token');
    }

    document.getElementById('formPelayanan').addEventListener('submit', function (e) {
        e.preventDefault();

        const token = getToken();
        if (!token) {
            Swal.fire('Error', 'Token tidak ditemukan. Silakan login ulang.', 'error');
            return;
        }

        const nama = document.getElementById('nama_pelayanan').value.trim();
        const deskripsi = document.getElementById('deskripsi').value.trim();
        const status = document.getElementById('status').value;

        const data = {
            nama_pelayanan: nama,
            deskripsi: deskripsi,
            status: status
        };

        fetch('/api/pelayanan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message || 'Pelayanan berhasil ditambahkan.',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });

                setTimeout(() => {
                    window.location.href = '{{ route("admin.pelayanan.index") }}';
                }, 1500);
            } else {
                Swal.fire('Gagal', response.message || 'Gagal menyimpan data.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data.', 'error');
        });
    });
</script>

@endsection
