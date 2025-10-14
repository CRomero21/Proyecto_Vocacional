
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 min-h-screen bg-gray-100">
    <div class="max-w-5xl mx-auto">
        <!-- Header mejorado -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-600 rounded-full mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-extrabold text-blue-600 mb-2">
                Test Vocacional
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto leading-relaxed">
                Descubre tu perfil profesional respondiendo estas preguntas. ¡Cada respuesta te acerca más a tu carrera ideal!
            </p>
        </div>

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <!-- Barra de Progreso Mejorada -->
            <div class="bg-blue-600 p-6">
                <div class="flex justify-between items-center mb-3">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span class="text-white font-semibold">Tu Progreso</span>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <span class="text-white font-bold text-sm" id="progress-text">Pregunta 1 de {{ count($preguntas) }}</span>
                    </div>
                </div>
                <div class="relative">
                    <div class="w-full bg-white/20 rounded-full h-4 shadow-inner">
                        <div class="bg-white h-4 rounded-full transition-all duration-500 ease-out shadow-lg" id="progress-bar" style="width: 0%"></div>
                    </div>
                    <div class="absolute top-0 left-0 w-full h-4 rounded-full bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-white/80">
                    <span>Inicio</span>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                        Meta
                    </span>
                </div>
            </div>

            <div class="p-8">
                <form action="{{ route('test.guardar') }}" method="POST" id="test-form">
                    @csrf
                    <input type="hidden" name="test_id" value="{{ $test_id }}">

                    @if(session('error'))
                        <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 mb-6 rounded-xl shadow-sm" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <p class="font-medium">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Guía visual de colores -->
                    

                    <!-- Contenedor de Preguntas Mejorado -->
                    <div id="questions-container" class="space-y-6 mb-8">
                        <!-- Las preguntas se cargarán aquí dinámicamente -->
                    </div>

                    <!-- Controles de Navegación Mejorados -->
                    <div class="bg-gray-100 rounded-2xl p-6 border border-gray-100">
                        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                            <button type="button" id="prev-btn"
                                    class="flex items-center px-4 md:px-6 py-2 md:py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl shadow-md transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 disabled:transform-none text-sm md:text-base">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Anterior
                            </button>

                            <div class="flex items-center space-x-2 md:space-x-3">
                                <span class="text-xs md:text-sm text-gray-600 font-medium">Página</span>
                                <div class="flex space-x-1 md:space-x-2" id="page-indicators">
                                    <!-- Los indicadores de página se generarán aquí -->
                                </div>
                                <span class="text-xs md:text-sm text-gray-600 font-medium">de <span id="total-pages">{{ ceil(count($preguntas) / 10) }}</span></span>
                            </div>

                            <button type="button" id="next-btn"
                                    class="flex items-center px-4 md:px-6 py-2 md:py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105 text-sm md:text-base">
                                <span id="next-btn-text">Siguiente</span>
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Botón Final Mejorado -->
                    <div class="mt-8 text-center hidden" id="submit-container">
                        <div class="bg-blue-600 rounded-2xl p-8 shadow-xl border border-blue-600">
                            <div class="mb-6">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-4 shadow-lg">
                                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-white mb-2">¡Felicitaciones! </h3>
                                <p class="text-white text-lg">Has completado todas las preguntas del test vocacional</p>
                            </div>
                            <button type="submit" id="submit-btn"
                                    class="inline-flex items-center px-8 py-4 bg-gray-400 text-gray-600 font-bold text-lg rounded-xl shadow-lg cursor-not-allowed opacity-60 transition-all duration-200">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Completa todas las preguntas para continuar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const preguntas = @json($preguntas);
    const questionsPerPage = 10; // Mostrar 10 preguntas por página
    let currentPage = 0;
    const totalPages = Math.ceil(preguntas.length / questionsPerPage);
    const answers = JSON.parse(localStorage.getItem('test_answers_' + {{ $test_id }}) || '{}');

    const container = document.getElementById('questions-container');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const nextBtnText = document.getElementById('next-btn-text');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const pageIndicators = document.getElementById('page-indicators');
    const submitContainer = document.getElementById('submit-container');
    const totalPagesSpan = document.getElementById('total-pages');

    // Actualizar contador total de páginas
    totalPagesSpan.textContent = totalPages;

    // Generar indicadores de página mejorados
    function generatePageIndicators() {
        pageIndicators.innerHTML = '';
        for (let i = 0; i < totalPages; i++) {
            const indicator = document.createElement('span');
            indicator.className = `relative w-6 h-6 md:w-8 md:h-8 rounded-full transition-all duration-300 transform ${
                i === currentPage
                    ? 'bg-white text-blue-600 shadow-lg ring-2 ring-white'
                    : 'bg-gray-300 text-blue-900'
            } flex items-center justify-center font-semibold text-xs md:text-sm select-none`;
            indicator.textContent = i + 1;
            indicator.title = `Página ${i + 1}`;
            pageIndicators.appendChild(indicator);
        }
    }

    // Mostrar preguntas de la página actual con mejor diseño
    function showQuestions(page) {
        const start = page * questionsPerPage;
        const end = Math.min(start + questionsPerPage, preguntas.length);
        const pageQuestions = preguntas.slice(start, end);

        container.innerHTML = '';

        pageQuestions.forEach((pregunta, index) => {
            const globalIndex = start + index;
            const questionDiv = document.createElement('div');
            questionDiv.className = 'bg-white p-6 md:p-6 rounded-2xl shadow-md border border-gray-100 animate-slide-in-up hover:shadow-lg transition-all duration-300';
            questionDiv.style.animationDelay = `${index * 0.1}s`;

            questionDiv.innerHTML = `
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold numero-pregunta-movil md:mr-5 text-lg shadow-lg">
                        ${globalIndex + 1}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 text-lg leading-relaxed mb-4 md:mb-6">${pregunta.texto}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 ml-17">
                    <label class="group respuesta-movil relative flex items-center w-full px-4 py-4 md:px-8 md:py-5 bg-gray-300 rounded-xl border-2 border-gray-300 hover:border-gray-400 hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:scale-105 active:scale-95">
                        <input type="radio" name="respuestas[${pregunta.id}]" value="0"
                               class="text-gray-700 focus:ring-gray-500 mr-4"
                               ${answers[pregunta.id] == '0' ? 'checked' : ''}>
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <div class="w-3 h-3 bg-gray-300 rounded-full mr-2"></div>
                                <div class="font-semibold text-gray-800 group-hover:text-gray-900">No me identifica</div>
                            </div>
                            <div class="text-sm text-gray-600 font-medium"></div>
                        </div>
                        ${answers[pregunta.id] == '1' ? '<div class="absolute top-2 right-2 w-6 h-6 bg-black rounded-full flex items-center justify-center"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>' : ''}
                    </label>
                    <label class="group respuesta-movil relative flex items-center w-full px-4 py-4 md:px-8 md:py-5 bg-blue-400 rounded-xl border-2 border-blue-400 hover:border-blue-500 hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:scale-105 active:scale-95">
                        <input type="radio" name="respuestas[${pregunta.id}]" value="1"
                               class="text-gray-700 focus:ring-gray-500 mr-4"
                               ${answers[pregunta.id] == '1' ? 'checked' : ''}>
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <div class="w-3 h-3 bg-blue-400 rounded-full mr-2"></div>
                                <div class="font-semibold text-white group-hover:text-gray-100">Me identifica un poco</div>
                            </div>
                            <div class="text-sm text-white/80 font-medium"></div>
                        </div>
                        ${answers[pregunta.id] == '1' ? '<div class="absolute top-2 right-2 w-6 h-6 bg-blue-400 rounded-full flex items-center justify-center"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>' : ''}
                    </label>
                    <label class="group respuesta-movil relative flex items-center w-full px-4 py-4 md:px-8 md:py-5 rounded-xl border-2 hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:scale-105 active:scale-95" style="background-color: #131e58; border-color: #131e58;">
                        <input type="radio" name="respuestas[${pregunta.id}]" value="2"
                               class="text-gray-700 focus:ring-gray-500 mr-4"
                               ${answers[pregunta.id] == '2' ? 'checked' : ''}>
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <div class="w-3 h-3 rounded-full mr-2" style="background-color: #131e58;"></div>
                                <div class="font-semibold text-white group-hover:text-gray-100">Me identifica mucho</div>
                            </div>
                            <div class="text-sm text-white/80 font-medium"></div>
                        </div>
                        ${answers[pregunta.id] == '2' ? '<div class="absolute top-2 right-2 w-6 h-6 bg-black rounded-full flex items-center justify-center"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>' : ''}
                    </label>
                </div>
            `;
            container.appendChild(questionDiv);
        });

        // Actualizar barra de progreso con animación
        const progress = ((page + 1) / totalPages) * 100;
        setTimeout(() => {
            progressBar.style.width = progress + '%';
        }, 100);

        // Actualizar texto de progreso
        const currentQuestion = Math.min((page + 1) * questionsPerPage, preguntas.length);
        progressText.textContent = `Pregunta ${currentQuestion} de ${preguntas.length}`;

        // Mostrar/ocultar botones con animación
        prevBtn.disabled = page === 0;
        nextBtnText.textContent = page === totalPages - 1 ? 'Finalizar Test' : 'Siguiente';

        // Mostrar contenedor de envío en la última página con animación
        if (page === totalPages - 1) {
            setTimeout(() => {
                submitContainer.classList.remove('hidden');
                submitContainer.classList.add('animate-bounce-in');
                // Verificar si el test está completo cuando se muestra el botón
                checkTestCompletion();
            }, 300);
            nextBtn.style.display = 'none';
        } else {
            submitContainer.classList.add('hidden');
            nextBtn.style.display = 'flex';
        }

        generatePageIndicators();
    }

    // Validar respuestas de la página actual con mejor feedback
    function validateCurrentPage() {
        const start = currentPage * questionsPerPage;
        const end = Math.min(start + questionsPerPage, preguntas.length);
        const pageQuestions = preguntas.slice(start, end);
        let unansweredCount = 0;
        let firstUnansweredQuestion = null;

        // Buscar la primera pregunta sin responder
        for (let i = 0; i < pageQuestions.length; i++) {
            const pregunta = pageQuestions[i];
            const radios = document.querySelectorAll(`input[name=\"respuestas[${pregunta.id}]\"]`);
            const checked = Array.from(radios).some(radio => radio.checked);
            if (!checked) {
                unansweredCount++;
                if (firstUnansweredQuestion === null) {
                    firstUnansweredQuestion = pregunta;
                }
            }
        }

        if (unansweredCount > 0) {
            // Mostrar notificación
            showNotification(`Faltan ${unansweredCount} pregunta${unansweredCount > 1 ? 's' : ''} por responder en esta página.`, 'warning');

            // Navegar automáticamente a la primera pregunta sin responder
            if (firstUnansweredQuestion) {
                setTimeout(() => {
                    const questionElement = document.querySelector(`input[name=\"respuestas[${firstUnansweredQuestion.id}]\"]`);
                    if (questionElement) {
                        // Encontrar el contenedor de la pregunta (el div padre con .bg-white.p-6)
                        let questionContainer = questionElement.closest('.bg-white.p-6');
                        if (!questionContainer) {
                            // fallback: buscar el div padre con .rounded-2xl.shadow-md
                            questionContainer = questionElement.closest('.rounded-2xl.shadow-md');
                        }
                        if (questionContainer) {
                            questionContainer.classList.add('ring-4', 'ring-red-400', 'ring-opacity-50');
                            questionContainer.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                            setTimeout(() => {
                                questionContainer.classList.remove('ring-4', 'ring-red-400', 'ring-opacity-50');
                            }, 3000);
                        }
                    }
                }, 500);
            }
            return false;
        }
        return true;
    }

    // Validar TODAS las preguntas del test completo
    function validateAllQuestions() {
        let unansweredCount = 0;
        let firstUnansweredQuestion = null;
        let firstUnansweredPage = -1;

        // Primero buscar preguntas sin responder en la página actual
        const currentPageStart = currentPage * questionsPerPage;
        const currentPageEnd = Math.min(currentPageStart + questionsPerPage, preguntas.length);
        const currentPageQuestions = preguntas.slice(currentPageStart, currentPageEnd);

        for (const pregunta of currentPageQuestions) {
            const radios = document.querySelectorAll(`input[name="respuestas[${pregunta.id}]"]`);
            const checked = Array.from(radios).some(radio => radio.checked);
            if (!checked) {
                unansweredCount++;
                if (firstUnansweredQuestion === null) {
                    firstUnansweredQuestion = pregunta;
                    firstUnansweredPage = currentPage;
                }
            }
        }

        // Si no hay preguntas sin responder en la página actual, buscar en todo el test
        if (unansweredCount === 0) {
            for (let page = 0; page < totalPages; page++) {
                const start = page * questionsPerPage;
                const end = Math.min(start + questionsPerPage, preguntas.length);
                const pageQuestions = preguntas.slice(start, end);
                for (const pregunta of pageQuestions) {
                    const radios = document.querySelectorAll(`input[name=\"respuestas[${pregunta.id}]\"]`);
                    const checked = Array.from(radios).some(radio => radio.checked);
                    if (!checked) {
                        unansweredCount++;
                        if (firstUnansweredQuestion === null) {
                            firstUnansweredQuestion = pregunta;
                            firstUnansweredPage = page;
                        }
                    }
                }
            }
        }

        if (unansweredCount > 0) {
            // Mostrar notificación
            showNotification(`Faltan ${unansweredCount} pregunta${unansweredCount > 1 ? 's' : ''} por responder en todo el test.`, 'warning');

            // Si la pregunta sin responder está en la página actual, solo hacer scroll
            if (firstUnansweredPage === currentPage) {
                setTimeout(() => {
                    if (firstUnansweredQuestion) {
                        const questionElement = document.querySelector(`input[name=\"respuestas[${firstUnansweredQuestion.id}]\"]`);
                        if (questionElement) {
                            let questionContainer = questionElement.closest('.bg-white.p-6');
                            if (!questionContainer) {
                                questionContainer = questionElement.closest('.rounded-2xl.shadow-md');
                            }
                            if (questionContainer) {
                                questionContainer.classList.add('ring-4', 'ring-red-400', 'ring-opacity-75', 'animate-pulse');
                                questionContainer.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                                setTimeout(() => {
                                    questionContainer.classList.remove('ring-4', 'ring-red-400', 'ring-opacity-75', 'animate-pulse');
                                }, 4000);
                            }
                        }
                    }
                }, 500);
            } else {
                // Si está en otra página, cambiar de página y luego resaltar
                setTimeout(() => {
                    currentPage = firstUnansweredPage;
                    showQuestions(currentPage);
                    setTimeout(() => {
                        if (firstUnansweredQuestion) {
                            const questionElement = document.querySelector(`input[name=\"respuestas[${firstUnansweredQuestion.id}]\"]`);
                            if (questionElement) {
                                let questionContainer = questionElement.closest('.bg-white.p-6');
                                if (!questionContainer) {
                                    questionContainer = questionElement.closest('.rounded-2xl.shadow-md');
                                }
                                if (questionContainer) {
                                    questionContainer.classList.add('ring-4', 'ring-red-400', 'ring-opacity-75', 'animate-pulse');
                                    questionContainer.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                                    setTimeout(() => {
                                        questionContainer.classList.remove('ring-4', 'ring-red-400', 'ring-opacity-75', 'animate-pulse');
                                    }, 4000);
                                }
                            }
                        }
                    }, 300);
                }, 1000);
            }
            return false;
        }
        return true;
    }

    // Verificar si todas las preguntas del test están completadas y actualizar el botón
    function checkTestCompletion() {
        // Usar las respuestas guardadas en localStorage en lugar del DOM actual
        const savedAnswers = JSON.parse(localStorage.getItem('test_answers_' + {{ $test_id }}) || '{}');

        let totalUnanswered = 0;
        let currentPageUnanswered = 0;

        // Obtener preguntas de la página actual
        const start = currentPage * questionsPerPage;
        const end = Math.min(start + questionsPerPage, preguntas.length);
        const currentPageQuestions = preguntas.slice(start, end);

        // Contar preguntas sin respuesta en la página actual
        for (const pregunta of currentPageQuestions) {
            if (!savedAnswers[pregunta.id]) {
                currentPageUnanswered++;
            }
        }

        // Si estamos en la última página, solo verificar la página actual
        // Si no estamos en la última página, verificar todas las preguntas
        if (currentPage === totalPages - 1) {
            totalUnanswered = currentPageUnanswered;
        } else {
            // Contar todas las preguntas sin respuesta guardada
            for (const pregunta of preguntas) {
                if (!savedAnswers[pregunta.id]) {
                    totalUnanswered++;
                }
            }
        }

        const submitBtn = document.getElementById('submit-btn');

        if (totalUnanswered === 0) {
            // Todas las preguntas están respondidas - habilitar botón
            submitBtn.disabled = false;
            submitBtn.className = 'inline-flex items-center px-8 py-4 bg-white hover:bg-gray-50 text-blue-600 font-bold text-lg rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105 cursor-pointer';
            submitBtn.innerHTML = `
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Ver Mis Resultados
            `;
        } else {
            // Faltan preguntas - deshabilitar botón
            submitBtn.disabled = true;
            submitBtn.className = 'inline-flex items-center px-8 py-4 bg-gray-400 text-gray-600 font-bold text-lg rounded-xl shadow-lg cursor-not-allowed opacity-60';
            submitBtn.innerHTML = `
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Completa todas las preguntas para continuar (${totalUnanswered} faltante${totalUnanswered > 1 ? 's' : ''})
            `;
        }

        return totalUnanswered === 0;
    }

    // Función para mostrar notificaciones
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg transform translate-x-full transition-all duration-300 ${
            type === 'warning' ? 'bg-yellow-500 text-white' :
            type === 'success' ? 'bg-green-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${
                        type === 'warning' ? 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z' :
                        type === 'success' ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' :
                        'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                    }"></path>
                </svg>
                <span class="font-medium">${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Animar entrada
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto-remover después de 4 segundos
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 4000);
    }

    // Guardar respuestas en localStorage con feedback
    function saveAnswers() {
        const formData = new FormData(document.getElementById('test-form'));
        // Obtener respuestas guardadas previamente
        const savedAnswers = JSON.parse(localStorage.getItem('test_answers_' + {{ $test_id }}) || '{}');
        const currentAnswers = { ...savedAnswers }; // Copiar respuestas guardadas

        // Actualizar con las respuestas de la página actual
        for (const [key, value] of formData.entries()) {
            if (key.startsWith('respuestas[')) {
                const preguntaId = key.match(/respuestas\[(\d+)\]/)[1];
                currentAnswers[preguntaId] = value;
            }
        }

        localStorage.setItem('test_answers_' + {{ $test_id }}, JSON.stringify(currentAnswers));

        // Feedback visual sutil de guardado automático
        const saveIndicator = document.createElement('div');
        saveIndicator.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-full shadow-lg transform translate-y-full transition-all duration-300 z-50';
        saveIndicator.innerHTML = '<span class="flex items-center"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Guardado</span>';
        document.body.appendChild(saveIndicator);

        setTimeout(() => saveIndicator.classList.remove('translate-y-full'), 100);
        setTimeout(() => {
            saveIndicator.classList.add('translate-y-full');
            setTimeout(() => document.body.removeChild(saveIndicator), 300);
        }, 2000);
    }

    // Navegar a una página específica con validación mejorada
    function goToPage(page) {
        // Solo permitir retroceder si la página actual está validada
        if (page > currentPage) {
            // Si intenta avanzar, validar primero
            if (!validateCurrentPage()) {
                // El scroll ya se maneja en validateCurrentPage
                return;
            }
        }
        saveAnswers();
        currentPage = page;
        showQuestions(currentPage);
        // Scroll suave hacia arriba
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Event listeners mejorados
    prevBtn.addEventListener('click', () => goToPage(currentPage - 1));
    nextBtn.addEventListener('click', () => {
        // Siempre validar antes de avanzar, en móvil y desktop
        if (!validateCurrentPage()) {
            // El scroll ya se maneja en validateCurrentPage
            return;
        }
        goToPage(currentPage + 1);
    });

    // Auto-guardado cuando cambian las respuestas
    container.addEventListener('change', (e) => {
        saveAnswers();
        // Actualizar el estado del botón si estamos en la última página
        if (currentPage === totalPages - 1) {
            checkTestCompletion();
        }
    });

    // Limpiar localStorage cuando se envía el formulario
    document.getElementById('test-form').addEventListener('submit', (e) => {
        // Verificar si todas las preguntas están completadas
        if (!checkTestCompletion()) {
            e.preventDefault();
            showNotification('Debes completar todas las preguntas del test antes de ver los resultados.', 'warning');
            return false;
        }

        localStorage.removeItem('test_answers_' + {{ $test_id }});
        showNotification('¡Enviando tus respuestas! Procesando resultados...', 'success');
    });

    // Inicializar con animación
    setTimeout(() => {
        showQuestions(0);
    }, 200);
});
</script>

<style>
@keyframes slide-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes bounce-in {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-slide-in-up {
    animation: slide-in-up 0.5s ease-out forwards;
}

.animate-bounce-in {
    animation: bounce-in 0.6s ease-out forwards;
}

/* Mejores estilos para los radio buttons */
input[type="radio"] {
    transform: scale(1.15);
    accent-color: currentColor;
    margin-right: 0.85rem;
}

input[type="radio"]:checked {
    animation: pulse 0.3s ease-in-out;
}

/* Hover effects mejorados */
.group:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Mejores efectos táctiles para móvil */
@media (hover: none) and (pointer: coarse) {
    .group:active {
        transform: scale(0.98);
        transition: transform 0.1s ease-out;
    }

    .group {
        transition: all 0.2s ease-out;
    }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .ml-17 {
        margin-left: 0;
    }

    /* Más separación entre número y pregunta */
    .numero-pregunta-movil {
        margin-right: 2.2rem !important;
    }

    /* Hacer los cuadros de respuesta más anchos */
    .respuesta-movil {
        box-sizing: border-box;
        display: flex;
        align-items: center;
        min-height: 3.1rem;
        font-size: 1.07rem;
    }

    /* Mejorar espaciado en móvil */
    .grid.grid-cols-1.md\\:grid-cols-3 {
        gap: 0.75rem;
    }

    /* Mejorar apariencia de las opciones en móvil */
    .group {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .group:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    /* Mejorar texto en móvil */
    .group .font-semibold {
        font-size: 0.95rem;
        line-height: 1.3;
    }

    /* Mejorar radio buttons en móvil */
    input[type="radio"] {
        transform: scale(1.3);
        margin-right: 0.75rem;
    }
}
</style>
@endpush
@endsection