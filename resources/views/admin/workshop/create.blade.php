@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Tambah Workshop</h4>

<div class="card">
    <div class="card-body">
        <form id="formWorkshop">
            <div class="mb-3">
                <label for="nama_workshop" class="form-label">Nama Workshop</label>
                <input type="text" class="form-control" id="nama_workshop" name="nama_workshop" required>
            </div>

            <div class="mb-3">
                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
            </div>

            <div class="mb-3">
                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
            </div>

            <div class="mb-3">
                <label for="lokasi" class="form-label">Lokasi</label>
                <input type="text" class="form-control" id="lokasi" name="lokasi" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.workshop.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<script>
    function getToken() {
        return localStorage.getItem('auth_token');
    }

    document.getElementById('formWorkshop').addEventListener('submit', function (e) {
        e.preventDefault();

        const token = getToken();
        if (!token) {
            Swal.fire('Error', 'Token tidak ditemukan. Silakan login ulang.', 'error');
            return;
        }

        const nama = document.getElementById('nama_workshop').value.trim();
        const mulai = document.getElementById('tanggal_mulai').value;
        const selesai = document.getElementById('tanggal_selesai').value;
        const lokasi = document.getElementById('lokasi').value.trim();

        if (new Date(selesai) < new Date(mulai)) {
            Swal.fire('Peringatan', 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.', 'warning');
            return;
        }

        const data = {
            nama_workshop: nama,
            tanggal_mulai: mulai,
            tanggal_selesai: selesai,
            lokasi: lokasi
        };

        fetch('/api/workshop', {
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
                    text: response.message || 'Workshop berhasil ditambahkan.',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });

                setTimeout(() => {
                    window.location.href = '{{ route("admin.workshop.index") }}';
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
