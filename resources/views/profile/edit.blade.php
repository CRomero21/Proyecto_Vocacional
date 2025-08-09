
@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="container mx-auto py-12">
    <div class="max-w-3xl mx-auto space-y-8 text-black">
        <div class="bg-white shadow-lg rounded-xl p-8 flex flex-col gap-6 text-black">
            <h2 class="text-2xl font-bold text-black mb-4 flex items-center gap-2">
                <svg class="w-7 h-7 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                Información de perfil
            </h2>
            @include('profile.partials.update-profile-information-form', ['user' => $user])
        </div>

        <div class="bg-white shadow-lg rounded-xl p-8 flex flex-col gap-6 text-black">
            <h2 class="text-2xl font-bold text-black mb-4 flex items-center gap-2">
                <svg class="w-7 h-7 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m0 4h.01M17.657 16.657A8 8 0 118.343 7.343a8 8 0 019.314 9.314z" /></svg>
                Cambiar contraseña
            </h2>
            @include('profile.partials.update-password-form', ['user' => $user])
        </div>

        <div class="bg-white shadow-lg rounded-xl p-8 flex flex-col gap-6 border border-red-200 text-black">
            <h2 class="text-2xl font-bold text-red-700 mb-4 flex items-center gap-2">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-1.414 1.414M6.343 17.657l-1.414-1.414M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Eliminar cuenta
            </h2>
            @include('profile.partials.delete-user-form', ['user' => $user])
        </div>
    </div>
</div>
@endsection