function initTraceabilitySearch() {
    const searchType = document.getElementById('search_type');
    const searchInput = document.getElementById('search_value');
    const resultsDiv = document.getElementById('traceability-results');

    if (!searchType || !searchInput) return;

    searchType.addEventListener('change', function () {
        searchInput.placeholder = getPlaceholder(this.value);
        searchInput.value = '';
        resultsDiv.innerHTML = '';
    });

    searchInput.addEventListener('input', debounce(function () {
        if (this.value.trim().length < 2) return;
        searchTraceability(searchType.value, this.value.trim());
    }, 300));
}

function getPlaceholder(type) {
    const placeholders = {
        'paciente': 'Buscar por nombre o CI del paciente...',
        'medicamento': 'Buscar por nombre de medicamento...',
        'lote': 'Buscar por número de lote...',
        'fecha': 'Buscar por fecha (DD/MM/AAAA)...'
    };
    return placeholders[type] || 'Buscar...';
}

function searchTraceability(type, value) {
    const resultsDiv = document.getElementById('traceability-results');
    resultsDiv.innerHTML = '<div class="text-center p-3"><div class="spinner-border text-primary"></div></div>';

    fetch(`/trazabilidad/buscarAjax?type=${encodeURIComponent(type)}&value=${encodeURIComponent(value)}`)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                resultsDiv.innerHTML = '<div class="alert alert-info">No se encontraron registros</div>';
                return;
            }
            let html = '<div class="table-responsive"><table class="data-table"><thead><tr>';
            html += '<th>Paciente</th><th>Medicamento</th><th>Lote</th><th>Vencimiento</th><th>Dispensación</th><th>Estado</th><th>Acciones</th>';
            html += '</tr></thead><tbody>';

            data.forEach(row => {
                html += `<tr>
                    <td>${row.paciente}</td>
                    <td>${row.medicamento}</td>
                    <td>${row.lote}</td>
                    <td>${row.vencimiento}</td>
                    <td>${row.fecha_dispensacion}</td>
                    <td><span class="badge ${row.estado === 'completada' ? 'badge-success' : 'badge-warning'}">${row.estado}</span></td>
                    <td><a href="/trazabilidad/detalle/${row.id}" class="btn btn-sm btn-info">Ver</a></td>
                </tr>`;
            });

            html += '</tbody></table></div>';
            resultsDiv.innerHTML = html;
        })
        .catch(() => {
            resultsDiv.innerHTML = '<div class="alert alert-danger">Error en la búsqueda</div>';
        });
}

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

document.addEventListener('DOMContentLoaded', function () {
    initTraceabilitySearch();
});