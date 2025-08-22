
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="flex items-center mb-8">
        <a href="{{ route('admin.carreras.index') }}" class="text-blue-600 hover:text-blue-800 mr-4 flex items-center transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver
        </a>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Detalles de la Carrera</h1>
    </div>

    <div class="bg-gradient-to-br from-blue-50 to-blue-100 shadow-lg rounded-2xl p-8 border border-blue-100">
        <div class="flex flex-col md:flex-row gap-8">
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-blue-900 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                    </svg>
                    {{ $carrera->nombre }}
                </h2>
                <div class="space-y-3 text-base text-gray-700">
                    <div>
                        <span class="font-semibold text-blue-700">Área de Conocimiento:</span>
                        <span>{{ $carrera->area_conocimiento }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-blue-700">Descripción:</span>
                        <span>{{ $carrera->descripcion }}</span>
                    </div>
                    @if($carrera->duracion)
                    <div>
                        <span class="font-semibold text-blue-700">Duración:</span>
                        <span>{{ $carrera->duracion }}</span>
                    </div>
                    @endif
                    @if($carrera->perfil_ingreso)
                    <div>
                        <span class="font-semibold text-blue-700">Perfil de Ingreso:</span>
                        <span>{{ $carrera->perfil_ingreso }}</span>
                    </div>
                    @endif
                    @if($carrera->perfil_egreso)
                    <div>
                        <span class="font-semibold text-blue-700">Perfil de Egreso:</span>
                        <span>{{ $carrera->perfil_egreso }}</span>
                    </div>
                    @endif
                    <div>
                        <span class="font-semibold text-blue-700">Institucional:</span>
                        <span>
                            @if($carrera->es_institucional)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-200 text-blue-800 text-xs font-semibold">Sí</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-200 text-gray-700 text-xs font-semibold">No</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex-1 flex flex-col items-center">
                @if($carrera->imagen)
                    <div class="mb-4 w-full flex justify-center">
                        <img src="{{ asset('storage/'.$carrera->imagen) }}" alt="Imagen de la carrera" class="rounded-xl shadow-lg max-h-52 object-contain border border-blue-200 bg-white p-2">
                    </div>
                @endif
                <div class="bg-white rounded-xl shadow p-5 w-full border border-blue-100">
                    <h3 class="font-semibold text-blue-700 mb-3 text-center text-lg">Perfil RIASEC</h3>
                    <div class="flex flex-col gap-2 text-base text-gray-700">
                        <span>
                            <span class="font-semibold text-blue-600">Tipo Primario:</span>
                            {{ $carrera->tipo_primario ?? '-' }}
                        </span>
                        <span>
                            <span class="font-semibold text-blue-600">Tipo Secundario:</span>
                            {{ $carrera->tipo_secundario ?? '-' }}
                        </span>
                        <span>
                            <span class="font-semibold text-blue-600">Tipo Terciario:</span>
                            {{ $carrera->tipo_terciario ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($carrera->universidades) && $carrera->universidades->count())
            <div class="mt-10">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Universidades que ofrecen esta carrera</h3>
                <ul class="list-disc pl-6 text-gray-700">
                    @foreach($carrera->universidades as $uni)
                        <li>{{ $uni->nombre }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-10 flex flex-col md:flex-row justify-end gap-3">
            <a href="{{ route('admin.carreras.edit', $carrera) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white font-bold py-2 px-5 rounded-lg shadow transition text-center">
                Editar
            </a>
            <form action="{{ route('admin.carreras.destroy', $carrera) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta carrera?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-5 rounded-lg shadow transition w-full md:w-auto">
                    Eliminar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection