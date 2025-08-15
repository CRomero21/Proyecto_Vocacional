// public/js/form-validation.js
document.addEventListener('DOMContentLoaded', function() {
    // Añadir más diagnósticos para depuración
    console.log('Script de validación cargado');
    
    // Detectar qué formulario está presente
    const isRegisterForm = document.querySelector('form[action*="register"]');
    const isCreateUserForm = document.querySelector('form[action*="usuarios"]');
    
    console.log('Formulario de registro detectado:', !!isRegisterForm);
    console.log('Formulario de creación detectado:', !!isCreateUserForm);
    
    if (!isRegisterForm && !isCreateUserForm) {
        console.log('No se detectó ningún formulario relevante');
        return; // Salir si no hay formulario relevante
    }
    
    // Encontrar campos del formulario (usar diferentes selectores según el formulario)
    const form = isRegisterForm ? document.querySelector('form[action*="register"]') : document.querySelector('form[action*="usuarios"]');
    
    // Solución para formularios que no tienen atributos action explícitos (como en Livewire)
    if (!form) {
        const allForms = document.querySelectorAll('form');
        for (let i = 0; i < allForms.length; i++) {
            if (allForms[i].querySelector('[name="password_confirmation"]')) {
                form = allForms[i];
                break;
            }
        }
    }
    
    if (!form) {
        console.error('No se pudo detectar el formulario');
        return;
    }
    
    console.log('Formulario detectado:', form);
    
    // Obtener referencias a los campos (con fallback para diferentes IDs)
    const nameInput = document.getElementById('name') || form.querySelector('[name="name"]');
    const emailInput = document.getElementById('email') || form.querySelector('[name="email"]');
    const departamentoSelect = document.getElementById('departamento') || form.querySelector('[name="departamento"]');
    const passwordInput = document.getElementById('password') || form.querySelector('[name="password"]');
    const passwordConfirmInput = document.getElementById('password_confirmation') || form.querySelector('[name="password_confirmation"]');
    
    console.log('Campos detectados:', {
        name: !!nameInput,
        email: !!emailInput,
        departamento: !!departamentoSelect,
        password: !!passwordInput,
        passwordConfirm: !!passwordConfirmInput
    });
    
    if (!nameInput || !emailInput || !passwordInput || !passwordConfirmInput) {
        console.error('No se pudieron encontrar todos los campos necesarios');
        return;
    }
    
    // Función para mostrar mensajes de error
    function showError(element, message) {
        console.log('Mostrando error:', message, 'para elemento:', element);
        
        // Primero verificar si el elemento tiene un div padre directo para Breeze/Jetstream
        let errorContainer = null;
        
        // Buscar el contenedor adecuado para el mensaje de error
        // Estrategia 1: Buscar el padre que tenga un div vacío para errores (común en Breeze)
        let parent = element.parentNode;
        let foundContainer = false;
        
        for (let i = 0; i < 5 && !foundContainer && parent; i++) {
            // Verificar si ya hay un contenedor de error
            const existingError = parent.querySelector('.text-red-600, .text-red-500, .text-sm.text-red-600');
            if (existingError) {
                errorContainer = existingError.parentNode;
                foundContainer = true;
                break;
            }
            
            // Para estructura de Breeze y otros
            const potentialContainer = parent.querySelector('.mt-2, .text-red-600, .text-red-500');
            if (potentialContainer) {
                errorContainer = potentialContainer.parentNode;
                foundContainer = true;
                break;
            }
            
            parent = parent.parentNode;
        }
        
        // Si no encontramos un contenedor adecuado, usamos el padre directo como último recurso
        if (!errorContainer) {
            errorContainer = element.parentNode;
        }
        
        // Eliminar error existente si hay
        const existingError = errorContainer.querySelector('.text-red-600, .text-red-500, .text-sm.text-red-600');
        if (existingError) {
            existingError.textContent = message; // Actualizar mensaje existente
            return;
        }
        
        // Crear y añadir nuevo mensaje de error
        const errorMsg = document.createElement('p');
        errorMsg.classList.add('mt-1', 'text-sm', 'text-red-600', 'validation-error');
        errorMsg.textContent = message;
        errorContainer.appendChild(errorMsg);
        
        // Resaltar el borde del campo
        element.classList.add('border-red-300', 'focus:border-red-300', 'focus:ring-red-300');
    }
    
    // Función para eliminar mensajes de error
    function clearError(element) {
        let parent = element.parentNode;
        let found = false;
        
        for (let i = 0; i < 5 && !found && parent; i++) {
            const existingError = parent.querySelector('.text-red-600, .text-red-500, .validation-error');
            if (existingError) {
                existingError.remove();
                found = true;
            }
            parent = parent.parentNode;
        }
        
        element.classList.remove('border-red-300', 'focus:border-red-300', 'focus:ring-red-300');
    }
    
    // El resto del código sigue igual...
    
    // Modificación específica para el formulario de registro:
    // Desactivar temporalmente el botón de envío para prevenir doble envío
    if (form) {
        const submitButton = form.querySelector('button[type="submit"]');
        
        if (submitButton) {
            // Añadir listener para resetear el botón en caso de error
            form.addEventListener('submit', function(e) {
                // Solo si no hay errores de validación, desactivar el botón
                if (!document.querySelector('.validation-error')) {
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-75', 'cursor-wait');
                    
                    // Reactivar después de 10 segundos (por si acaso hay un problema)
                    setTimeout(function() {
                        submitButton.disabled = false;
                        submitButton.classList.remove('opacity-75', 'cursor-wait');
                    }, 10000);
                }
            });
        }
    }
    
    // Resto del código sin cambios...
});