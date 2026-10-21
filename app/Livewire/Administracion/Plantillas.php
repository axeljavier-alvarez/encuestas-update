<?php

namespace App\Livewire\Administracion;

use App\Models\Plantilla;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Plantillas extends Component
{
    use WithPagination;

    #[Url]
    public string $buscar = '';

    public bool $mostrarModalPlantilla = false;

    public ?int $plantillaId = null;
    public string $codigoPlantilla = '';
    public string $nombrePlantilla = '';
    public string $descripcionPlantilla = '';

    public ?int $plantillaEstadoId = null;
    public string $nombrePlantillaEstado = '';
    public bool $nuevoEstadoPlantilla = false;

    public function crearPlantilla(): void
    {
        $this->limpiarFormulario();
        $this->mostrarModalPlantilla = true;
    }

    public function editar(int $plantillaId): void
    {
        $plantilla = Plantilla::findOrFail($plantillaId);

        $this->limpiarFormulario();

        $this->plantillaId = $plantilla->id;
        $this->codigoPlantilla = $plantilla->codigo;
        $this->nombrePlantilla = $plantilla->nombre;
        $this->descripcionPlantilla = $plantilla->descripcion ?? '';

        $this->mostrarModalPlantilla = true;
    }

    public function guardarPlantilla(): void
    {
        $datos = $this->validate([
            'codigoPlantilla' => [
                'required',
                'string',
                'max:10',
                Rule::unique('plantillas', 'codigo')
                    ->ignore($this->plantillaId),
            ],
            'nombrePlantilla' => [
                'required',
                'string',
                'max:150',
            ],
            'descripcionPlantilla' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $esEdicion = $this->plantillaId !== null;

        $valores = [
            'codigo' => trim($datos['codigoPlantilla']),
            'nombre' => trim($datos['nombrePlantilla']),
            'descripcion' => $datos['descripcionPlantilla'] ?? null,
        ];

        if ($esEdicion) {
            $plantilla = Plantilla::findOrFail($this->plantillaId);
            $plantilla->update($valores);
        } else {
            Plantilla::create($valores);
        }

        $this->cerrarModalPlantilla();

        session()->flash(
            'mensaje',
            $esEdicion
                ? 'La plantilla fue actualizada correctamente.'
                : 'La plantilla fue creada correctamente.'
        );
    }

    public function limpiarFormulario(): void
    {
        $this->reset([
            'plantillaId',
            'codigoPlantilla',
            'nombrePlantilla',
            'descripcionPlantilla',
        ]);

        $this->resetValidation();
    }

    public function cerrarModalPlantilla(): void
    {
        $this->mostrarModalPlantilla = false;
        $this->limpiarFormulario();
    }

    public function updatedBuscar(): void
    {
        $this->resetPage();
    }

    
    // cambiar estado
    public function confirmarCambioEstado(int $plantillaId)
    {
        $plantilla = Plantilla::findOrFail($plantillaId);
        $this->plantillaEstadoId = $plantilla->id;
        $this->nombrePlantillaEstado = $plantilla->nombre;
        $this->nuevoEstadoPlantilla = !$plantilla->activo;

        $this->modal('confirmar-estado-plantilla')->show();
    }

    public function cambiarEstadoPlantilla(): void
    {
        if(!$this->plantillaEstadoId){
            return;
        }
        $plantilla = Plantilla::findOrFail($this->plantillaEstadoId);
        $plantilla->activo = $this->nuevoEstadoPlantilla;
        $plantilla->save();
        $mensaje = $this->nuevoEstadoPlantilla
        ? 'La plantilla fue activada correctamente.'
        : 'La plantilla fue desactivada correctamente.';
        $this->modal('confirmar-estado-plantilla')->close();
        $this->plantillaEstadoId = null;
        $this->nombrePlantillaEstado = '';
        session()->flash('mensaje', $mensaje);

    }
    

    public function render()
    {
        $plantillas = Plantilla::query()
            ->when($this->buscar !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery
                        ->where('codigo', 'like', '%' . $this->buscar . '%')
                        ->orWhere('nombre', 'like', '%' . $this->buscar . '%');
                });
            })
            ->latest('id')
            ->paginate(10);

        return view('livewire.administracion.plantillas', [
            'plantillas' => $plantillas,
            'totalPlantillas' => Plantilla::count(),
            'totalActivas' => Plantilla::where('activo', true)->count(),
            'totalInactivas' => Plantilla::where('activo', false)->count(),
        ]);
    }

}
