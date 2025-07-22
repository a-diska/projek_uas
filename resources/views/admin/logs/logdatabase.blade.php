@extends('admin.index')

@section('content')
    <h4 class="fw-bold py-3 mb-4">Log Database</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Log Database</h5>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr class="text-center">
                        <th style="width: 50px;">No</th>
                        <th style="width: 80px;">Method</th>
                        <th>User Agent</th>
                        <th style="width: 120px;">IP</th>
                        <th style="width: 160px;">Tanggal</th>
                        <th>List</th>
                        <th>Table</th>
                        <th>ID Table</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody id="tabel-log-database">
                    <tr>
                        <td colspan="9" class="text-center text-muted">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-end">
            <nav id="pagination-db-container"></nav>
        </div>
    </div>

    <style>
        code {
            display: block;
            overflow-x: auto;
            max-height: 200px;
            font-size: 13px;
        }
    </style>

    <script>
        let currentDbPage = 1;

        document.addEventListener('DOMContentLoaded', () => {
            loadLogDatabase(currentDbPage);
        });

        function loadLogDatabase(page = 1) {
            const token = localStorage.getItem('auth_token');
            const tbody = document.getElementById("tabel-log-database");

            if (!token) {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Token tidak ditemukan. Harap login ulang.</td></tr>`;
                return;
            }

            fetch(`/api/logs/database?page=${page}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(response => {
                const data = response.data;
                tbody.innerHTML = "";

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach((log, index) => {
                        tbody.innerHTML += `
                            <tr>
                                <td class="text-center">${(response.from || 0) + index}</td>
                                <td class="text-center">
                                    <span class="badge bg-label-${getBadgeColor(log.method)}">${log.method}</span>
                                </td>
                                <td><span title="${log.agent}">${log.agent.slice(0, 40)}...</span></td>
                                <td class="text-center">${log.ip}</td>
                                <td class="text-center">${formatTanggal(log.tanggal)}</td>
                                <td>${log.list}</td>
                                <td class="text-center">${log.table}</td>
                                <td class="text-center">${log.id_table}</td>
                                <td><code>${formatJSON(log.data)}</code></td>
                            </tr>
                        `;
                    });

                    renderPaginationDB(response);
                } else {
                    tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted">Data log belum tersedia.</td></tr>`;
                    document.getElementById('pagination-db-container').innerHTML = '';
                }
            })
            .catch(error => {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">${error.message}</td></tr>`;
            });
        }

        function renderPaginationDB(response) {
            const container = document.getElementById('pagination-db-container');
            let html = '<ul class="pagination pagination-sm mb-0">';

            if (response.prev_page_url) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); loadLogDatabase(${response.current_page - 1})">&laquo;</a></li>`;
            }

            for (let i = 1; i <= response.last_page; i++) {
                html += `<li class="page-item ${i === response.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="event.preventDefault(); loadLogDatabase(${i})">${i}</a>
                </li>`;
            }

            if (response.next_page_url) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); loadLogDatabase(${response.current_page + 1})">&raquo;</a></li>`;
            }

            html += '</ul>';
            container.innerHTML = html;
        }

        function formatTanggal(tanggal) {
            const d = new Date(tanggal);
            return d.toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function getBadgeColor(method) {
            switch (method) {
                case 'POST': return 'success';
                case 'PUT': return 'warning';
                case 'DELETE': return 'danger';
                default: return 'secondary';
            }
        }

        function formatJSON(json) {
            try {
                return JSON.stringify(JSON.parse(json), null, 2);
            } catch (e) {
                return json;
            }
        }
    </script>
@endsection
