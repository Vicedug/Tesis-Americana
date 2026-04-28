document.addEventListener('DOMContentLoaded', function () {
    initSidebar();
    initTooltips();
    initConfirmDeletes();
    initFlashMessages();
});

function initSidebar() {
    const currentPath = window.location.pathname;
    const links = document.querySelectorAll('.sidebar-menu a');
    links.forEach(link => {
        if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href').replace('/', ''))) {
            link.classList.add('active');
        }
    });
}

function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(el => {
        el.addEventListener('mouseenter', function () {
            const tip = document.createElement('div');
            tip.className = 'tooltip-text';
            tip.textContent = this.dataset.tooltip;
            this.appendChild(tip);
        });
        el.addEventListener('mouseleave', function () {
            const tip = this.querySelector('.tooltip-text');
            if (tip) tip.remove();
        });
    });
}

function initConfirmDeletes() {
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('¿Está seguro de que desea eliminar este registro?')) {
                e.preventDefault();
            }
        });
    });
}

function initFlashMessages() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
}

function searchPaciente() {
    const ci = document.getElementById('search_ci').value.trim();
    if (!ci) return;

    fetch(`/paciente/buscarAjax?ci=${encodeURIComponent(ci)}`)
        .then(res => res.json())
        .then(data => {
            const result = document.getElementById('paciente-result');
            if (data.found) {
                result.innerHTML = `
                    <div class="alert alert-success">
                        <strong>Paciente encontrado:</strong> ${data.nombre} ${data.apellido} - CI: ${data.ci}
                        ${data.asegurado ? '<span class="badge badge-success">Asegurado</span>' : '<span class="badge badge-danger">No asegurado</span>'}
                    </div>
                    <input type="hidden" name="paciente_id" value="${data.id}">
                `;
            } else {
                result.innerHTML = `
                    <div class="alert alert-warning">Paciente no encontrado. ¿Desea <a href="/paciente/crear?ci=${ci}">registrar</a>?</div>
                `;
            }
        })
        .catch(() => {
            document.getElementById('paciente-result').innerHTML = '<div class="alert alert-danger">Error en la búsqueda</div>';
        });
}

function searchMedicamento() {
    const term = document.getElementById('search_medicamento').value.trim();
    if (term.length < 2) return;

    fetch(`/farmacia/buscarAjax?term=${encodeURIComponent(term)}`)
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('medicamento_id');
            select.innerHTML = '<option value="">Seleccionar medicamento</option>';
            data.forEach(med => {
                select.innerHTML += `<option value="${med.id}">${med.nombre} - ${med.forma_farmaceutica} (${med.concentracion})</option>`;
            });
        });
}

function validateForm(formId) {
    const form = document.getElementById(formId);
    let valid = true;
    form.querySelectorAll('[required]').forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            valid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    return valid;
}

function formatCI(ci) {
    return ci.replace(/\D/g, '');
}

function formatNumber(num) {
    return new Intl.NumberFormat('es-PY').format(num);
}