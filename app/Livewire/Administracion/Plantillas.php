<?php

namespace App\Livewire\Administracion;

use App\Models\Plantilla;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Pregunta;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Componente Livewire para administrar plantillas de evaluación.
 *
 * Permite crear y editar plantillas, buscar registros, asignar hasta 10 preguntas
 * activas a una plantilla con validaciones específicas (incluyendo el límite de
 * dos preguntas NPS) y consultar las preguntas actualmente asociadas a cada
 * plantilla.
 */
class Plantillas extends Component
{
    use WithPagination;

    /**
     * Término usado para filtrar plantillas por código o nombre.
     */
    #[Url]
    public string $buscar = '';

    /**
     * Indica si el modal de creación/edición de plantilla está abierto.
     */
    public bool $mostrarModalPlantilla = false;

    /**
     * Código de la plantilla actual en el formulario.
     */
    public string $codigoPlantilla = '';

    /**
     * Nombre de la plantilla actual en el formulario.
     */
    public string $nombrePlantilla = '';

    /**
     * Descripción de la plantilla actual en el formulario.
     */
    public string $descripcionPlantilla = '';

    /**
     * Identificador de la plantilla en edición, o null si se está creando.
     */
    public ?int $plantillaId = null;

    /**
     * Modal para seleccionar preguntas y asignarlas a una plantilla.
     */
    public bool $mostrarModalPreguntas = false;

    /**
     * Filtro aplicado a la lista de preguntas: todos, nps o no_nps.
     */
    public string $filtroNps = 'todos';

    /**
     * Plantilla que está recibiendo la asociación de preguntas.
     */
    public ?int $plantillaSeleccionadaId = null;

    /**
     * IDs de preguntas seleccionadas para la plantilla actual.
     */
    public array $preguntasSeleccionadas = [];

    /**
     * Modal para visualizar las preguntas ya asignadas a una plantilla.
     */
    public bool $mostrarModalPreguntasAsignadas = false;

    /**
     * Plantilla consultada para ver sus preguntas asignadas.
     */
    public ?int $plantillaConsultaId = null;

    // orden temporal de las preguntas
    public array $ordenPreguntas = [];

    // pregunta nueva
    public ?int $preguntaNuevaId = null;

    // controlar la visualización del modal de vista previa
    public bool $mostrarModalFormulario = false;
    // id de la plantilla que se está previsualizando
    public ?int $plantillaFormularioId = null;

    /**
     * Abre el formulario para crear una nueva plantilla.
     */
    public function crearPlantilla(): void
    {
        // Abre el modal para crear una nueva plantilla.
        $this->limpiarFormulario();
        $this->mostrarModalPlantilla = true;
    }

    /**
     * Valida y persiste los datos de una plantilla existente o nueva.
     *
     * @throws \Illuminate\Validation\ValidationException Si los campos no cumplen la validación.
     */
    public function guardarPlantilla(): void
    {
        // Valida código, nombre y descripción antes de guardar.
        $datos = $this->validate([
            'codigoPlantilla' => [
                'required',
                'string',
                'max:10',
                Rule::unique('plantillas', 'codigo')->ignore($this->plantillaId),
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
        $datosPlantilla = [
            'codigo' => $datos['codigoPlantilla'],
            'nombre' => $datos['nombrePlantilla'],
            'descripcion' => $datos['descripcionPlantilla']
        ];
        if ($esEdicion) {
            // Edita la plantilla existente.
            $plantilla = Plantilla::findOrFail(
                $this->plantillaId
            );
            $plantilla->update($datosPlantilla);
        } else {
            // Crea una nueva plantilla.
            Plantilla::create($datosPlantilla);
        }
        $this->cerrarModalPlantilla();
        session()->flash('mensaje', $esEdicion ? 'La plantilla fue actualizada correctamente' : 'La plantilla fue creada correctamente');
    }

    /**
     * Carga los datos de una plantilla para editarla en el modal.
     */
    public function editar(int $plantillaId): void
    {
        $plantilla = Plantilla::findOrFail($plantillaId);
        $this->plantillaId = $plantilla->id;
        $this->codigoPlantilla = $plantilla->codigo;
        $this->nombrePlantilla = $plantilla->nombre;
        $this->descripcionPlantilla = $plantilla->descripcion ?? '';
        $this->mostrarModalPlantilla = true;
    }

    /**
     * Reinicia el formulario de plantilla y limpia sus validaciones.
     */
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

    /**
     * Cierra el modal de plantilla y limpia el estado del formulario.
     */
    public function cerrarModalPlantilla(): void
    {
        $this->mostrarModalPlantilla = false;
        $this->limpiarFormulario();
    }

    /**
     * Reinicia la paginación de plantillas cuando cambia la búsqueda.
     */
    public function updatedBuscar(): void
    {
        $this->resetPage();
    }

    /**
     * Abre el modal para seleccionar preguntas para una nueva asignación.
     *
     * Se limpia también el filtro y la selección previa para empezar una nueva
     * operación sin dejar estado residual.
     */
    public function verPreguntas(): void
    {
        // Limpia la selección previa y abre el modal de preguntas.
        $this->reset([
            'filtroNps',
            'plantillaSeleccionadaId',
            'preguntasSeleccionadas'
        ]);
        $this->resetValidation();
        $this->resetPage(pageName: 'preguntasPage');
        $this->mostrarModalPreguntas = true;
    }
    /**
     * Cierra el modal de asignación de preguntas y limpia la selección.
     */
    public function cerrarModalPreguntas(): void
    {
        $this->mostrarModalPreguntas = false;
        $this->reset([
            'filtroNps',
            'plantillaSeleccionadaId',
            'preguntasSeleccionadas',
        ]);
        $this->resetValidation();
    }

    /**
     * Reinicia la paginación del catálogo de preguntas cuando cambia el filtro.
     */
    public function updatedFiltroNps(): void
    {
        $this->resetPage(pageName: 'preguntasPage');
    }

    /**
     * Añade o quita una pregunta de la selección actual.
     *
     * Restringe la selección a 10 preguntas totales y a un máximo de 2 NPS.
     */
    public function alternarPregunta(int $preguntaId): void
    {
        $this->resetValidation();

        // Quita la pregunta si ya estaba elegida.
        if (in_array($preguntaId, $this->preguntasSeleccionadas)) {
            $this->preguntasSeleccionadas = array_values(
                array_diff($this->preguntasSeleccionadas, [$preguntaId])
            );

            return;
        }

        $pregunta = Pregunta::query()
            ->where('activo', true)
            ->findOrFail($preguntaId);

        // Limita la selección a 10 preguntas como máximo.
        if (count($this->preguntasSeleccionadas) >= 10) {
            $this->addError(
                'preguntasSeleccionadas',
                'Solamente puede seleccionar 10 preguntas.'
            );

            return;
        }

        $npsSeleccionadas = Pregunta::query()
            ->whereIn('id', $this->preguntasSeleccionadas)
            ->where('es_nps', true)
            ->count();

        // No puede haber más de 2 preguntas NPS en la plantilla.
        if ($pregunta->es_nps && $npsSeleccionadas >= 2) {
            $this->addError(
                'preguntasSeleccionadas',
                'Solamente puede seleccionar 2 preguntas NPS.'
            );

            return;
        }

        $this->preguntasSeleccionadas[] = $preguntaId;
    }

    /**
     * Guarda la selección de preguntas de una plantilla.
     *
     * Requiere exactamente 10 preguntas activas y exactamente 2 preguntas NPS.
     * Además, bloquea la operación si la plantilla ya tenía preguntas asignadas.
     *
     * @throws \Illuminate\Validation\ValidationException Si la plantilla ya tiene preguntas.
     */
    public function guardarPreguntas(): void
    {
        // Verifica que haya exactamente 10 preguntas y que sean válidas.
        $this->validate([
            'plantillaSeleccionadaId' => [
                'required',
                'exists:plantillas,id',
            ],
            'preguntasSeleccionadas' => [
                'required',
                'array',
                'size:10',
            ],
            'preguntasSeleccionadas.*' => [
                'integer',
                'distinct',
                'exists:preguntas,id',
            ],
        ], [
            'plantillaSeleccionadaId.required' =>
            'Debe seleccionar una plantilla.',

            'preguntasSeleccionadas.required' =>
            'Debe seleccionar las preguntas.',

            'preguntasSeleccionadas.size' =>
            'Debe seleccionar exactamente 10 preguntas.',
        ]);

        $ids = $this->preguntasSeleccionadas;

        // Confirma que todas las preguntas seleccionadas estén activas.
        $preguntas = Pregunta::query()
            ->whereIn('id', $ids)
            ->where('activo', true)
            ->get();

        if ($preguntas->count() !== 10) {
            $this->addError(
                'preguntasSeleccionadas',
                'Todas las preguntas seleccionadas deben estar activas.'
            );

            return;
        }

        if ($preguntas->where('es_nps', true)->count() !== 2) {
            $this->addError(
                'preguntasSeleccionadas',
                'Debe seleccionar exactamente 2 preguntas NPS.'
            );

            return;
        }

        DB::transaction(function () use ($ids) {

            // Bloquea la plantilla para evitar asignaciones concurrentes.
            $plantilla = Plantilla::query()
                ->lockForUpdate()
                ->findOrFail($this->plantillaSeleccionadaId);

            if ($plantilla->preguntas()->exists()) {
                throw ValidationException::withMessages([
                    'plantillaSeleccionadaId' =>
                    'Esta plantilla ya tiene preguntas asignadas.',
                ]);
            }

            // Guarda la relación con el orden exacto de las preguntas.
            $preguntasConOrden = [];
            foreach ($ids as $indice => $preguntaId) {
                $preguntasConOrden[$preguntaId] = [
                    'orden' => $indice + 1
                ];
            }
            $plantilla->preguntas()->sync($preguntasConOrden);
        });

        $this->cerrarModalPreguntas();

        session()->flash(
            'mensaje',
            'Las 10 preguntas fueron asignadas correctamente.'
        );
    }
    /**
     * Abre el modal para consultar las preguntas ya asignadas a una plantilla.
     */
    public function verPreguntasAsignadas(int $plantillaId): void
    {
        $plantilla = Plantilla::with('preguntas')
            ->findOrFail($plantillaId);

        $this->plantillaConsultaId = $plantillaId;

        $this->ordenPreguntas = $plantilla->preguntas
            ->sortBy('pivot.orden')
            ->pluck('id')
            ->values()
            ->all();
        $this->resetValidation();
        $this->mostrarModalPreguntasAsignadas = true;
    }

    public function moverPregunta(int $indice, string $direccion): void
    {
        $total = count($this->ordenPreguntas);
        if ($direccion === 'arriba') {
            $nuevoIndice = $indice - 1;
        } elseif ($direccion === 'abajo') {
            $nuevoIndice = $indice + 1;
        } else {
            return;
        }
        // no salir de los limites
        if (
            $indice < 0 || $indice >= $total ||
            $nuevoIndice < 0 || $nuevoIndice >= $total
        ) {
            return;
        }
        // intercambiar posiciones en el arreglo
        [
            $this->ordenPreguntas[$indice],
            $this->ordenPreguntas[$nuevoIndice]
        ] = [
            $this->ordenPreguntas[$nuevoIndice],
            $this->ordenPreguntas[$indice]
        ];
    }
    public function guardarOrdenPreguntas(): void
    {
        // se detiene cuando no hay plantilla seleccionada
        if (!$this->plantillaConsultaId) {
            return;
        }
        $ids = array_map('intval', $this->ordenPreguntas);
        if (count($ids) !== 10 || count($ids) !== count(array_unique($ids))) {
            $this->addError(
                'ordenPreguntas',
                'Debe tener exactamente 10 preguntas para guardar.'
            );
            return;
        }
        $preguntas = Pregunta::whereIn('id', $ids)
            ->where('activo', true)
            ->get();
        if (
            $preguntas->count() !== 10 ||
            $preguntas->where('es_nps', true)->count() !== 2
        ) {

            $this->addError(
                'ordenPreguntas',
                'Debe seleccionar 10 preguntas activas, incluyendo exactamente 2 NPS.'
            );
            return;
        }

        DB::transaction(function () use ($ids) {
            $plantilla = Plantilla::lockForUpdate()
                ->findOrFail($this->plantillaConsultaId);
            $datos = [];
            foreach ($ids as $indice => $id) {
                $datos[$id] = ['orden' => $indice + 1];
            }
            $plantilla->preguntas()->sync($datos);
        });
        $this->cerrarPreguntasAsignadas();

        session()->flash(
            'mensaje',
            'Las preguntas y su orden se actualizaron correctamente.'
        );
    }

    /**
     * Cierra el modal de consulta de preguntas asignadas.
     */
    public function cerrarPreguntasAsignadas(): void
    {
        $this->mostrarModalPreguntasAsignadas = false;
        $this->plantillaConsultaId = null;
        $this->preguntaNuevaId = null;
        $this->ordenPreguntas = [];
        $this->resetValidation();
    }

    // eliminar temporalmente una pregunta
    public function quitarPregunta(int $preguntaId): void
    {
        $this->ordenPreguntas = array_values(
            array_diff($this->ordenPreguntas, [$preguntaId])
        );
        $this->resetValidation();
    }
    // agregar pregunta
    public function agregarPregunta(): void
    {
        $this->validate([
            'preguntaNuevaId' => ['required', 'exists:preguntas,id'],
        ]);
        $pregunta = Pregunta::where('activo', true)
            ->findOrFail($this->preguntaNuevaId);
        if (
            count($this->ordenPreguntas) >= 10 ||
            in_array($pregunta->id, $this->ordenPreguntas)
        ) {
            return;
        }
        $cantidadNps = Pregunta::whereIn('id', $this->ordenPreguntas)
            ->where('es_nps', true)
            ->count();
        if ($pregunta->es_nps && $cantidadNps >= 2) {
            $this->addError(
                'preguntaNuevaId',
                'Solamente puede agregar 2 preguntas NPS'
            );
            return;
        }
        $this->ordenPreguntas[] = $pregunta->id;
        $this->preguntaNuevaId = null;
        $this->resetValidation();
    }

    // ver formulario
    public function verFormulario(int $plantillaId): void
    {
        // comprobar que la plantilla exista
        Plantilla::findOrFail($plantillaId);
        // guardar id de la plantilla seleccionada
        $this->plantillaFormularioId = $plantillaId;
        // abrir modal de vista previa
        $this->mostrarModalFormulario = true;
    }

    public function cerrarFormulario(): void
    {
        $this->mostrarModalFormulario = false;
        // Elimina la referencia a la plantilla anterior.
        $this->plantillaFormularioId = null;
    }
    /**
     * Prepara los datos para renderizar la vista principal del módulo.
     *
     * Incluye la lista de plantillas, el catálogo de preguntas filtradas,
     * las plantillas sin preguntas, la cantidad de NPS seleccionadas y la
     * información de la plantilla consultada.
     */
    public function render()
    {
        // Lista plantillas con búsqueda y paginación.
        $plantillas = Plantilla::query()
            ->when(
                $this->buscar,
                function ($query) {
                    $query->where(function ($subQuery) {
                        $subQuery
                            ->where('codigo', 'like', '%' . $this->buscar . '%')
                            ->orWhere('nombre', 'like', '%' . $this->buscar . '%');
                    });
                }
            )
            ->latest('id')
            ->paginate(10);
        // Carga preguntas activas y aplica el filtro NPS.
        $preguntasActivas = Pregunta::query()
            ->with(['factor', 'tema'])
            ->where('activo', true)
            ->when($this->filtroNps === 'nps', function ($query) {
                $query->where('es_nps', true);
            })
            ->when($this->filtroNps === 'no_nps', function ($query) {
                $query->where('es_nps', false);
            })
            ->orderBy('id')
            ->paginate(20, ['*'], 'preguntasPage');
        // Muestra solo plantillas sin preguntas aún asignadas.
        $plantillasDisponibles = Plantilla::query()
            ->doesntHave('preguntas')
            ->orderBy('nombre')
            ->get();

        // Cuenta cuántas preguntas NPS están seleccionadas.
        $cantidadNps = Pregunta::query()
            ->whereIn('id', $this->preguntasSeleccionadas)
            ->where('es_nps', true)
            ->count();
        // Trae la plantilla consultada junto con sus preguntas y relaciones.
        $plantillaConsulta = $this->plantillaConsultaId
            ? Plantilla::with([
                'preguntas.factor',
                'preguntas.tema'
            ])->find($this->plantillaConsultaId)
            : null;
        $preguntasDisponibles = Pregunta::query()
            ->where('activo', true)
            ->whereNotIn('id', $this->ordenPreguntas)
            ->orderBy('id')
            ->get();
        // recuperar plantilla y sus reguntas para vista previa
        $plantillaFormulario = $this->plantillaFormularioId
            ? Plantilla::with([
                'preguntas.tipoRespuesta',
                'preguntas.escala',
                'preguntas.opcionesRespuesta',
            ])->find($this->plantillaFormularioId)
            : null;
        // ordenar preguntas segun tabla pivote
        if ($plantillaFormulario) {
            $plantillaFormulario->setRelation(
                'preguntas',
                $plantillaFormulario->preguntas
                    ->sortBy('pivot.orden')
                    ->values()
            );
        }
        return view(
            'livewire.administracion.plantillas',
            [
                'plantillas' => $plantillas,
                'preguntasActivas' => $preguntasActivas,
                'plantillasDisponibles' => $plantillasDisponibles,
                'cantidadNps' => $cantidadNps,
                'plantillaConsulta' => $plantillaConsulta,
                'preguntasDisponibles' => $preguntasDisponibles,
                'plantillaFormulario' => $plantillaFormulario
            ]
        );
    }
}
