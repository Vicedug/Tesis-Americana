function validateRequired(formId) {
    const form = document.getElementById(formId);
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        removeError(field);
        if (!field.value.trim()) {
            showError(field, 'Este campo es obligatorio');
            isValid = false;
        }
    });

    return isValid;
}

function showError(field, message) {
    field.classList.add('is-invalid');
    let errorDiv = field.parentElement.querySelector('.invalid-feedback');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        field.parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
}

function removeError(field) {
    field.classList.remove('is-invalid');
    const errorDiv = field.parentElement.querySelector('.invalid-feedback');
    if (errorDiv) errorDiv.remove();
}

function validateCI(fieldId) {
    const field = document.getElementById(fieldId);
    const ci = field.value.replace(/\D/g, '');
    if (ci.length < 5) {
        showError(field, 'CI debe tener al menos 5 dígitos');
        return false;
    }
    removeError(field);
    return true;
}

function validateEmail(fieldId) {
    const field = document.getElementById(fieldId);
    const email = field.value.trim();
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regex.test(email)) {
        showError(field, 'Email no válido');
        return false;
    }
    removeError(field);
    return true;
}

function validateDate(fieldId) {
    const field = document.getElementById(fieldId);
    const date = field.value;
    if (date && isNaN(Date.parse(date))) {
        showError(field, 'Fecha no válida');
        return false;
    }
    removeError(field);
    return true;
}

function validateNumber(fieldId, min = 0) {
    const field = document.getElementById(fieldId);
    const num = parseFloat(field.value);
    if (isNaN(num) || num < min) {
        showError(field, `Debe ser un número mayor o igual a ${min}`);
        return false;
    }
    removeError(field);
    return true;
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-control').forEach(field => {
        field.addEventListener('input', function () {
            removeError(this);
        });
    });
});