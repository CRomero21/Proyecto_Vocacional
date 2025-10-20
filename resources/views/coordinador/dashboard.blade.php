@extends('layouts.app')

@section('content')
<div class="min-h-screen" style="background: linear-gradient(180deg, #f2f2f2 0%, #c8c8c8 3%, #f2f2f2 100%);">
    <!-- Header -->
    <div class="shadow-sm border-b relative" style="background:#ffffff; border-color:#c8c8c8;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <svg class="w-10 h-10 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#0b3be9;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Panel del Coordinador
                    </h1>
                    <p class="mt-1" style="color:#131e58;">Resumen esencial del progreso de estudiantes</p>
                </div>
                <div class="text-right">
                    <p class="text-sm" style="color:#131e58; opacity:.8;">Última actualización</p>
                    <p class="text-sm font-medium" style="color:#051a9a;">{{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
        <!-- Accent bar under header -->
        <div class="absolute bottom-0 left-0 w-full h-1" style="background:linear-gradient(90deg,#0b3be9,#0079f4,#00aeff);"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @php
            $totalEst = (int) ($totalEstudiantes ?? 0);
            $totalComp = (int) ($totalTests ?? 0);
            $totales = (int) ($testsTotales ?? $totalComp);
            $rate = $totales > 0 ? round(($totalComp / $totales) * 100) : 0;
            $semana = (int) ($testsUltimaSemana ?? 0);
            $promedioDiario = $semana > 0 ? round($semana / 7, 1) : 0;
            $enCurso = max($totales - $totalComp, 0);
            // Total de retroalimentaciones (acepta distintas variables del controlador)
            $retroTotal = (int) ($totalRetroalimentacion ?? $totalRetroalimentaciones ?? 0);
        @endphp
        <!-- Mensaje de Error si existe -->
        @if(isset($error))
            <div class="mb-8 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @endif

        

    <!-- Métricas esenciales (tiles en colores sólidos) -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            <!-- Total Estudiantes -->
            <div class="metric-tile" style="background:#051a9a;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="metric-label">Total Estudiantes</p>
                        <p class="metric-value">{{ number_format($totalEst) }}</p>
                        <p class="metric-sub">Usuarios con rol estudiante</p>
                    </div>
                    <div class="metric-icon">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#ffffff; opacity:.9;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tests Completados -->
            <div class="metric-tile" style="background:#0b3be9;">
                <div class="flex items-center justify-between">
                    <div class="flex-1 mr-4">
                        <p class="metric-label">Tests Completados</p>
                        <p class="metric-value">{{ number_format($totalComp) }}</p>
                        <div class="mt-2 h-2 w-full rounded-full" style="background:rgba(255,255,255,.25);">
                            <div class="h-2 rounded-full" style="width: {{ $rate }}%; background:#00aeff;"></div>
                        </div>
                        <p class="metric-sub">Tasa de finalización {{ $rate }}%</p>
                    </div>
                    <div class="metric-icon">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#ffffff; opacity:.9;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            

            <!-- Última semana -->
            <div class="metric-tile" style="background:#0079f4;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="metric-label">Últimos 7 días</p>
                        <p class="metric-value">{{ number_format($semana) }}</p>
                        <p class="metric-sub">≈ {{ $promedioDiario }} por día</p>
                    </div>
                    <div class="metric-icon">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#ffffff; opacity:.9;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividad reciente (compacta) -->
        <div class="rounded-xl shadow-sm p-6 hover-lift" style="background:linear-gradient(180deg,#f2f2f2 0%, #ffffff 45%); border:1px solid #c8c8c8;">
            <h3 class="text-lg font-semibold mb-2 flex items-center" style="color:#051a9a;">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#0079f4;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Actividad reciente
            </h3>
            <div class="h-0.5 w-24 mb-4" style="background:linear-gradient(90deg,#0b3be9,#00aeff);"></div>
            <div class="divide-y divide-gray-100">
                @forelse(($ultimosTests ?? []) as $test)
                    <div class="py-2.5 px-2 activity-row flex items-center justify-between group rounded">
                        <div class="min-w-0 mr-4 flex items-start">
                            <span class="inline-block w-1 h-8 mr-3 rounded" style="background:linear-gradient(180deg,#0b3be9,#00aeff);"></span>
                            <div>
                                <p class="text-sm font-medium truncate" style="color:#131e58;">{{ $test->user->name ?? 'Usuario desconocido' }}</p>
                                <p class="text-xs mt-0.5" style="color:#131e58; opacity:.8;">
                                    @if($test->completado)
                                        <span class="px-2 py-0.5 rounded-full" style="background:#0b3be922; color:#0b3be9;">Completó test</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full" style="background:#c8c8c8; color:#131e58;">Inició test</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="text-xs whitespace-nowrap" style="color:#0079f4;">{{ $test->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                @empty
                    <div class="text-center py-6 text-sm" style="color:#131e58; opacity:.8;">No hay actividad reciente</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
<style>
.hover-lift { transition: transform .18s ease, box-shadow .18s ease; }
.hover-lift:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,.08); }
.accent-bar { position:absolute; top:0; left:0; height:4px; width:100%; background:linear-gradient(90deg,#00aeff,#0079f4); border-top-left-radius:12px; border-top-right-radius:12px; }
.activity-row { transition: background .18s ease, box-shadow .18s ease, transform .18s ease; }
.activity-row:hover { background: linear-gradient(90deg,#f2f2f2, #ffffff); box-shadow: inset 4px 0 0 #00aeff; transform: translateY(-1px); }
/* Metric tiles */
.metric-tile { color:#ffffff; border-radius: 16px; padding: 20px; box-shadow: 0 6px 18px rgba(0,0,0,.08); position: relative; overflow: hidden; }
.metric-tile:before { content:""; position:absolute; right:-40px; top:-40px; width:160px; height:160px; border-radius: 50%; background: rgba(255,255,255,.08); }
.metric-tile:after  { content:""; position:absolute; right:-10px; bottom:-30px; width:110px; height:110px; border-radius: 50%; background: rgba(255,255,255,.06); }
.metric-label { font-size:.85rem; letter-spacing:.02em; opacity:.9; }
.metric-value { font-size:2.25rem; font-weight:800; line-height: 1.1; margin-top:.15rem; }
.metric-sub { font-size:.75rem; opacity:.85; margin-top:.35rem; }
.metric-icon { display:flex; align-items:center; justify-content:center; background: rgba(255,255,255,.12); border-radius: 12px; padding: 10px; }
.metric-tile:hover { transform: translateY(-2px); box-shadow: 0 14px 32px rgba(0,0,0,.12); }
</style>
@endsection