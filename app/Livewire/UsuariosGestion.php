<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class UsuariosGestion extends Component
{
    public $buscar = '';

    public function render()
    {
        $usuarios = \App\Models\User::when($this->buscar, function ($q) {
            $q->where('name', 'like', '%' . $this->buscar . '%')
            ->orWhere('email', 'like', '%' . $this->buscar . '%');
        })->get();

        return view('livewire.usuarios-gestion', compact('usuarios'));
    }
}