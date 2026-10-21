<div class="mx-auto w-full max-w-7xl px-2 sm:px-4 space-y-4">

    {{-- ENCABEZADO PRINCIPAL --}}

    <div class="overflow-hidden rounded-2xl px-5 py-5 text-white shadow-lg sm:px-6"
        style="background: linear-gradient(110deg, #020617 0%, #172554 50%, #312e81 100%);">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            {{-- LOGOTIPO E INFORMACIÓN --}}
            <div class="flex min-w-0 items-center gap-4">

                {{-- ESCUDO MUNICIPAL --}}
                <div class="shrink-0">
                    <img src="{{ asset('images/icono_muni.png') }}" alt="Municipalidad de Guatemala" width="75"
                        height="75"
                        style="width: 75px !important;
                           height: 75px !important;
                           max-width: 75px !important;
                           max-height: 75px !important;
                           object-fit: contain;
                           flex-shrink: 0;" />
                </div>

                {{-- INFORMACIÓN DEL MÓDULO --}}
                <div class="min-w-0 flex-1">

                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-blue-200">
                        Administración
                    </p>

                    <h1 class="mt-1 text-xl font-black tracking-tight text-white sm:text-2xl">
                        Plantillas de evaluación
                    </h1>

                    <p class="mt-1.5 max-w-xl text-xs leading-5 text-blue-100 sm:text-sm">
                        Gestione las plantillas y consulte las preguntas
                        asociadas a cada una.
                    </p>

                </div>

            </div>
            {{-- BOTONES DE ACCIONES --}}
            <div class="flex w-full flex-wrap items-center gap-3 lg:w-auto lg:shrink-0">

                {{-- VER PREGUNTAS --}}
                <flux:button type="button" wire:click="verPreguntas" variant="outline" icon="list-bullet"
                    class="flex-1 font-bold shadow-sm sm:flex-none"
                    style="background-color: #DBEAFE !important;
               color: #1E3A8A !important;
               border: 1px solid #BFDBFE !important;">
                    Ver preguntas
                </flux:button>

                {{-- NUEVA PLANTILLA --}}
                <flux:button type="button" wire:click="crearPlantilla" variant="primary" icon="plus"
                    class="flex-1 font-bold shadow-md sm:flex-none"
                    style="background-color: #FFFFFF !important;
               color: #1E3A8A !important;
               border: 1px solid #FFFFFF !important;">
                    Nueva plantilla
                </flux:button>
            </div>
        </div>
    </div>

    {{--  mensaje --}}
    @if (session('mensaje'))
        <div x-data="{ mostrar: true }" x-show="mostrar" x-init="setTimeout(() => mostrar = false, 4000)"
            class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
            {{ session('mensaje') }}
        </div>
    @endif
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 space-y-4">
        <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-700">
                    Formularios disponibles
                </p>
                <h2 class="mt-1 text-xl font-black tracking-tight text-slate-950">
                    Listado de plantillas
                </h2>
                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Consulte las preguntas asociadas a cada plantilla.
                </p>
            </div>
            <div class="w-full sm:w-80">
                <flux:input wire:model.live.debounce.300ms="buscar" placeholder="Buscar plantilla..."
                    icon="magnifying-glass" clearable />
            </div>
        </div>
        {{-- TABLA --}}
        <flux:table :paginate="$plantillas">
            <flux:table.columns>
                <flux:table.column>
                    Código
                </flux:table.column>
                <flux:table.column>
                    Plantilla
                </flux:table.column>
                <flux:table.column class="text-center">
                    Estado
                </flux:table.column>
                <flux:table.column align="center">
                    Acciones
                </flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($plantillas as $plantilla)
                    <flux:table.row wire:key="plantilla-{{ $plantilla->id }}">
                        <flux:table.cell>
                            <flux:badge variant="pill" color="blue" size="sm">
                                {{ $plantilla->codigo }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <p class="text-sm font-bold text-slate-900">
                                {{ $plantilla->nombre }}
                            </p>
                            <p class="mt-1 text-[11px] text-slate-500">
                                {{ $plantilla->descripcion ?: 'Sin descripción registrada.' }}
                            </p>
                        </flux:table.cell>
                        <flux:table.cell class="text-center">
                            <flux:badge variant="pill" :color="$plantilla->activo ? 'emerald' : 'zinc'" size="sm">
                                {{ $plantilla->activo ? 'Activa' : 'Inactiva' }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-center">
                            <div class="flex flex-wrap items-center justify-center gap-2">

                                <flux:button size="sm" variant="subtle" wire:click="editar({{ $plantilla->id }})">
                                    Editar
                                </flux:button>

                                <flux:button size="sm" variant="primary"
                                    wire:click="verPreguntasAsignadas({{ $plantilla->id }})">
                                    Ver preguntas
                                </flux:button>

                                {{-- Vista previa del formulario --}}
                                <flux:button size="sm" variant="outline" icon="eye"
                                    wire:click="verFormulario({{ $plantilla->id }})">
                                    Ver formulario
                                </flux:button>

                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="py-8 text-center">
                            <p class="text-sm font-black text-slate-700">
                                No se encontraron plantillas
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Registre una nueva plantilla para comenzar.
                            </p>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
    <flux:modal wire:model="mostrarModalPlantilla" class="max-w-lg space-y-6">
        <div>
            <flux:heading size="lg">
                {{ $plantillaId ? 'Editar plantilla' : 'Nueva plantilla' }}
            </flux:heading>
            <flux:subheading>
                Registre una nueva plantilla de evaluación.
            </flux:subheading>
        </div>
        <form wire:submit="guardarPlantilla" class="space-y-4">
            <flux:input wire:model="codigoPlantilla" label="Código" placeholder="Ej. CURSO-01" maxlength="10" />

            @error('codigoPlantilla')
                <p class="text-xs font-bold text-red-600">
                    {{ $message }}
                </p>
            @enderror


            <flux:input wire:model="nombrePlantilla" label="Nombre" placeholder="Nombre de la plantilla" />

            @error('nombrePlantilla')
                <p class="text-xs font-bold text-red-600">
                    {{ $message }}
                </p>
            @enderror


            <flux:textarea wire:model="descripcionPlantilla" label="Descripción" rows="3"
                placeholder="Descripción de la plantilla..." />

            @error('descripcionPlantilla')
                <p class="text-xs font-bold text-red-600">
                    {{ $message }}
                </p>
            @enderror


            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">

                <flux:button type="button" variant="subtle" wire:click="cerrarModalPlantilla">
                    Cancelar
                </flux:button>


                <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                    wire:target="guardarPlantilla">

                    <span wire:loading.remove wire:target="guardarPlantilla">
                        Guardar
                    </span>

                    <span wire:loading wire:target="guardarPlantilla">
                        Guardando...
                    </span>

                </flux:button>
            </div>
        </form>
    </flux:modal>
    {{--  MODAL PREGUNTAS ACTIVAS --}}
    {{-- MODAL PREGUNTAS ACTIVAS --}}
    {{-- MODAL: ASIGNAR PREGUNTAS A PLANTILLA --}}
    <flux:modal wire:model="mostrarModalPreguntas" class="w-full max-w-6xl">
        <div class="space-y-5">

            {{-- ENCABEZADO --}}
            <div>
                <flux:heading size="lg">
                    Banco de preguntas activas
                </flux:heading>

                <flux:subheading>
                    Seleccione una plantilla y asigne exactamente
                    10 preguntas, incluyendo 2 NPS.
                </flux:subheading>
            </div>

            {{-- SELECCIÓN DE PLANTILLA --}}
            <div class="rounded-xl border border-slate-200 p-4 space-y-3">

                <flux:heading size="sm">
                    Plantilla de evaluación
                </flux:heading>

                <flux:select wire:model="plantillaSeleccionadaId" placeholder="Seleccione una plantilla">
                    <flux:select.option value="">
                        Seleccione una plantilla
                    </flux:select.option>

                    @foreach ($plantillasDisponibles as $plantilla)
                        <flux:select.option value="{{ $plantilla->id }}">
                            {{ $plantilla->nombre }}
                            ({{ $plantilla->codigo }})
                        </flux:select.option>
                    @endforeach

                </flux:select>

                @if ($plantillasDisponibles->isEmpty())
                    <p class="text-xs text-amber-700">
                        No hay plantillas pendientes de asignación.
                    </p>
                @endif

                @error('plantillaSeleccionadaId')
                    <p class="text-xs font-bold text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- CONTADORES --}}
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                <div class="rounded-xl bg-blue-50 p-4">
                    <p class="text-xs font-semibold text-blue-700">
                        Preguntas seleccionadas
                    </p>

                    <p class="mt-1 text-2xl font-black text-blue-950">
                        {{ count($preguntasSeleccionadas) }} / 10
                    </p>
                </div>

                <div class="rounded-xl bg-violet-50 p-4">
                    <p class="text-xs font-semibold text-violet-700">
                        Preguntas NPS seleccionadas
                    </p>

                    <p class="mt-1 text-2xl font-black text-violet-950">
                        {{ $cantidadNps }} / 2
                    </p>
                </div>

            </div>

            {{-- FILTRO --}}
            <div class="flex flex-wrap items-center justify-between gap-3">

                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        Preguntas disponibles
                    </h3>

                    <p class="text-xs text-slate-500">
                        {{ $preguntasActivas->total() }}
                        preguntas encontradas
                    </p>
                </div>

                <div class="w-full sm:w-52">
                    <flux:select wire:model.live="filtroNps">

                        <flux:select.option value="todos">
                            Todas
                        </flux:select.option>

                        <flux:select.option value="nps">
                            Solo NPS
                        </flux:select.option>

                        <flux:select.option value="no_nps">
                            Sin NPS
                        </flux:select.option>

                    </flux:select>
                </div>

            </div>

            {{-- LISTADO EN DOS COLUMNAS --}}
            <div class="max-h-[440px] overflow-y-auto pr-1">

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">

                    @forelse ($preguntasActivas as $pregunta)
                        <label wire:key="pregunta-banco-{{ $pregunta->id }}"
                            class="flex cursor-pointer items-start gap-3
                               rounded-xl border border-slate-200
                               p-4 transition hover:bg-slate-50">

                            <input type="checkbox"
                                class="mt-1 h-4 w-4 rounded border-slate-300
                                   text-blue-600 focus:ring-blue-500"
                                @checked(in_array($pregunta->id, $preguntasSeleccionadas)) wire:click="alternarPregunta({{ $pregunta->id }})" />

                            <div class="min-w-0 flex-1 space-y-2">

                                <div class="flex items-start justify-between gap-2">

                                    <span class="text-xs font-bold text-slate-400">
                                        #{{ $pregunta->id }}
                                    </span>

                                    @if ($pregunta->es_nps)
                                        <flux:badge size="sm" color="violet">
                                            NPS
                                        </flux:badge>
                                    @endif

                                </div>

                                <p class="text-sm font-semibold text-slate-900">
                                    {{ $pregunta->texto }}
                                </p>

                                <div class="flex flex-wrap gap-2">

                                    <flux:badge size="sm" color="blue">
                                        {{ $pregunta->factor?->nombre ?? 'Sin factor' }}
                                    </flux:badge>

                                    @if ($pregunta->tema)
                                        <flux:badge size="sm" color="zinc">
                                            {{ $pregunta->tema->nombre }}
                                        </flux:badge>
                                    @endif

                                </div>

                            </div>

                        </label>

                    @empty

                        <div class="col-span-full py-10 text-center">

                            <p class="text-sm font-semibold text-slate-500">
                                No se encontraron preguntas activas.
                            </p>

                        </div>
                    @endforelse

                </div>

            </div>

            {{-- PAGINACIÓN --}}
            <div class="border-t border-slate-100 pt-3">

                {{ $preguntasActivas->links() }}

            </div>

            {{-- ERRORES DE SELECCIÓN --}}
            @error('preguntasSeleccionadas')
                <div class="rounded-lg bg-red-50 px-4 py-3">
                    <p class="text-sm font-semibold text-red-700">
                        {{ $message }}
                    </p>
                </div>
            @enderror

            {{-- ACCIONES --}}
            <div class="flex justify-end gap-2 border-t border-slate-200 pt-4">

                <flux:button type="button" variant="subtle" wire:click="cerrarModalPreguntas">
                    Cancelar
                </flux:button>

                <flux:button type="button" variant="primary" wire:click="guardarPreguntas"
                    wire:loading.attr="disabled" wire:target="guardarPreguntas">
                    <span wire:loading.remove wire:target="guardarPreguntas">
                        Guardar selección
                    </span>

                    <span wire:loading wire:target="guardarPreguntas">
                        Guardando...
                    </span>
                </flux:button>

            </div>

        </div>
    </flux:modal>

    {{-- MODAL: CONSULTAR PREGUNTAS ASIGNADAS --}}
    <flux:modal wire:model="mostrarModalPreguntasAsignadas" class="w-full max-w-5xl">
        <div class="flex min-h-0 flex-col gap-4 overflow-hidden" style="height: min(760px, calc(100dvh - 110px));">

            {{-- ENCABEZADO DEL MODAL --}}
            <div class="shrink-0 border-b border-slate-200 pb-4">

                <div class="flex items-center gap-3">

                    {{-- ESCUDO MUNICIPAL --}}
                    <img src="{{ asset('images/icono_muni.png') }}" alt="Municipalidad de Guatemala" width="55"
                        height="55"
                        style="width: 55px !important;
                   height: 55px !important;
                   max-width: 55px !important;
                   max-height: 55px !important;
                   object-fit: contain;
                   flex-shrink: 0;" />

                    {{-- INFORMACIÓN --}}
                    <div class="min-w-0 flex-1">

                        <flux:heading size="lg">
                            Preguntas de la plantilla
                        </flux:heading>

                        @if ($plantillaConsulta)
                            <flux:subheading class="mt-1">
                                {{ $plantillaConsulta->nombre }}
                                {{ $plantillaConsulta->codigo }}
                            </flux:subheading>
                        @endif

                    </div>

                </div>

            </div>

            @if ($plantillaConsulta)
                <div class="grid shrink-0 grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <p class="text-xs font-semibold text-blue-700">
                            Preguntas asignadas
                        </p>
                        <p class="mt-1 text-2xl font-black text-blue-950">
                            {{ count($ordenPreguntas) }} / 10
                        </p>
                    </div>
                    <div class="rounded-xl border border-violet-100 bg-biolet-50 p-4">
                        <p class="text-xs font-semibold text-violet-700">
                            Preguntas NPS
                        </p>
                        <p class="mt-1 text-2xl font-black text-violet-950">
                            {{ \App\Models\Pregunta::whereIn('id', $ordenPreguntas)->where('es_nps', true)->count() }}
                            / 2
                        </p>
                    </div>
                </div>
                <!-- encabezado listado -->
                <div class="flex shrink-0 items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">
                            Preguntas del formulario
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Preguntas organizadas según su orden de presentación.
                        </p>
                    </div>

                    <flux:badge color="zinc" size="sm">
                        {{ count($ordenPreguntas) }} preguntas
                    </flux:badge>
                </div>
                {{-- LISTADO DE PREGUNTAS --}}
                <div class="min-h-0 flex-1 w-full overflow-y-auto overscroll-contain pr-2">
                    @if (count($ordenPreguntas) > 0)
                        <div class="space-y-3">
                            @foreach ($ordenPreguntas as $indice => $preguntaId)
                                @php
                                    $pregunta =
                                        $plantillaConsulta->preguntas->firstWhere('id', $preguntaId) ??
                                        // mostrar pregunta nueva que no pertenece a la plantilla
                                        \App\Models\Pregunta::with(['factor', 'tema'])->find($preguntaId);
                                @endphp
                                @if (!$pregunta)
                                    @continue
                                @endif

                                <div wire:key="asignada-{{ $plantillaConsulta->id }}-{{ $pregunta->id }}"
                                    class="w-full rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                                    <div class="flex items-center gap-4">

                                        {{-- NÚMERO DE ORDEN --}}
                                        <div class="shrink-0 self-start">
                                            <x-numero-orden :numero="$indice + 1" />
                                        </div>

                                        {{-- CONTENIDO DE LA PREGUNTA --}}
                                        <div class="min-w-0 flex-1">

                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <span
                                                    class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                                    Pregunta {{ $indice + 1 }}
                                                </span>

                                                <flux:badge :color="$pregunta->es_nps ? 'violet' : 'zinc'"
                                                    size="sm">
                                                    {{ $pregunta->es_nps ? 'NPS' : 'General' }}
                                                </flux:badge>
                                            </div>

                                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-900">
                                                {{ $pregunta->texto }}
                                            </p>

                                            <div
                                                class="mt-3 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3">

                                                <flux:badge color="blue" size="sm">
                                                    Factor:
                                                    {{ $pregunta->factor?->nombre ?? 'Sin factor' }}
                                                </flux:badge>

                                                <flux:badge color="zinc" size="sm">
                                                    Tema:
                                                    {{ $pregunta->tema?->nombre ?? 'Sin tema' }}
                                                </flux:badge>

                                                <flux:badge color="zinc" size="sm">
                                                    Tipo: {{ $pregunta->tipo ?? 'Sin tipo' }}
                                                </flux:badge>

                                            </div>
                                        </div>

                                        {{-- FLECHAS PARA CAMBIAR ORDEN --}}
                                        <div class="flex shrink-0 flex-col items-center gap-2">

                                            <flux:button type="button" size="sm" variant="outline"
                                                icon="chevron-up"
                                                wire:click="moverPregunta({{ $indice }}, 'arriba')"
                                                :disabled="$indice === 0" title="Subir pregunta" />

                                            <flux:button type="button" size="sm" variant="outline"
                                                icon="chevron-down"
                                                wire:click="moverPregunta({{ $indice }}, 'abajo')"
                                                :disabled="$indice === count($ordenPreguntas) - 1"
                                                title="Bajar pregunta" />

                                        </div>
                                        {{-- QUITAR PREGUNTA --}}
                                        <flux:button type="button" size="sm" variant="ghost" icon="x-mark"
                                            class="text-red-500 hover:text-red-700"
                                            wire:click="quitarPregunta({{ $pregunta->id }})"
                                            title="Quitar pregunta" />

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div
                            class="flex h-full min-h-full w-full flex-col
               items-center justify-center
               rounded-xl border border-dashed
               border-slate-300 bg-slate-50
               px-6 py-10 text-center">

                            <h3 class="text-sm font-bold text-slate-800">
                                Esta plantilla todavía no tiene preguntas asignadas.
                            </h3>

                            <p class="mt-2 max-w-md text-xs leading-5 text-slate-500">
                                Utilice el banco de preguntas para seleccionar
                                y asignar las 10 preguntas correspondientes
                                a esta plantilla de evaluación.
                            </p>

                        </div>
                    @endif
                </div>
            @endif
            {{-- PIE FIJO: NO SE DESPLAZA CON LAS PREGUNTAS --}}
            <div class="shrink-0 space-y-3 border-t border-slate-200 bg-white pt-3">
                {{-- AGREGAR PREGUNTA: PERMANECE FIJA SOBRE LOS BOTONES --}}
                @if ($plantillaConsulta && count($ordenPreguntas) > 0 && count($ordenPreguntas) < 10)
                    <div class="mt-4 rounded-xl border border-dashed border-blue-200 bg-blue-50 p-4">

                        <p class="mb-3 text-sm font-bold text-blue-900">
                            Agregar pregunta ({{ count($ordenPreguntas) }}/10)
                        </p>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">

                            <div class="min-w-0 flex-1">
                                <flux:select wire:model="preguntaNuevaId" placeholder="Seleccione una pregunta">
                                    <flux:select.option value="">
                                        Seleccione una pregunta
                                    </flux:select.option>

                                    @foreach ($preguntasDisponibles as $disponible)
                                        <flux:select.option value="{{ $disponible->id }}">
                                            #{{ $disponible->id }} -
                                            {{ $disponible->texto }}
                                            {{ $disponible->es_nps ? '(NPS)' : '' }}
                                        </flux:select.option>
                                    @endforeach

                                </flux:select>
                            </div>

                            <flux:button type="button" variant="primary" icon="plus"
                                wire:click="agregarPregunta">
                                Agregar
                            </flux:button>

                        </div>

                        @error('preguntaNuevaId')
                            <p class="mt-2 text-xs font-semibold text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>
                @endif

                @error('ordenPreguntas')
                    <p class="text-sm font-semibold text-red-600">
                        {{ $message }}
                    </p>
                @enderror
                <div class="flex justify-end gap-3">
                    <flux:button type="button" variant="subtle" wire:click="cerrarPreguntasAsignadas">
                        Cancelar
                    </flux:button>
                    <flux:button type="button" variant="primary" wire:click="guardarOrdenPreguntas"
                        wire:loading.attr="disabled" wire:target="guardarOrdenPreguntas">
                        <span wire:loading.remove wire:target="guardarOrdenPreguntas">
                            Guardar orden
                        </span>
                        <span wire:loading wire:target="guardarOrdenPreguntas">
                            Guardando...
                        </span>
                    </flux:button>
                </div>
            </div> {{-- FIN DEL PIE FIJO --}}
        </div>
    </flux:modal>

    {{-- MODAL: VISTA PREVIA DEL FORMULARIO --}}
    <flux:modal wire:model="mostrarModalFormulario" class="w-full max-w-5xl">
        <div class="flex flex-col gap-4" style="height: min(820px, calc(100dvh - 100px));">

            {{-- ENCABEZADO --}}
            <div class="flex shrink-0 items-center justify-between gap-3 border-b border-slate-200 pb-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/icono_muni.png') }}" alt="Municipalidad de Guatemala" width="75"
                        height="75"
                        style="width:75px!important;height:75px!important;object-fit:contain;flex-shrink:0;" />

                    <div>
                        <flux:heading size="lg">Vista previa del formulario</flux:heading>
                        <flux:subheading>Visualización de la encuesta para el ciudadano</flux:subheading>
                    </div>
                </div>

                <flux:badge color="blue" size="sm">Vista previa</flux:badge>
            </div>

            {{-- CONTENIDO --}}
            <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain rounded-xl bg-slate-100 p-3 sm:p-6">

                @if ($plantillaFormulario)
                    <div class="mx-auto max-w-3xl space-y-5">

                        {{-- INFORMACIÓN DE LA PLANTILLA --}}
                        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                            <div class="h-3 bg-gradient-to-r from-blue-950 via-blue-800 to-indigo-700"></div>

                            <div class="space-y-4 p-6 sm:p-8">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('images/icono_muni.png') }}" alt="Municipalidad de Guatemala"
                                        class="h-14 w-14 object-contain" />

                                    <div>
                                        <p class="text-xs font-black uppercase tracking-widest text-blue-800">
                                            Municipalidad de Guatemala
                                        </p>
                                        <p class="text-xs text-slate-500">Evaluación de satisfacción</p>
                                    </div>
                                </div>

                                <div class="border-t border-slate-100 pt-5">
                                    <h2 class="text-2xl font-black text-slate-950 sm:text-3xl">
                                        {{ $plantillaFormulario->nombre }}
                                    </h2>

                                    @if ($plantillaFormulario->descripcion)
                                        <p class="mt-3 text-sm text-slate-600">
                                            {{ $plantillaFormulario->descripcion }}
                                        </p>
                                    @endif

                                    <p class="mt-4 text-sm leading-6 text-slate-500">
                                        Su opinión es importante para mejorar nuestros programas y servicios.
                                        Seleccione la respuesta que mejor represente su experiencia.
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <flux:badge color="blue">{{ $plantillaFormulario->codigo }}</flux:badge>
                                    <flux:badge color="zinc">
                                        {{ $plantillaFormulario->preguntas->count() }} preguntas
                                    </flux:badge>
                                </div>

                                <p class="text-xs font-medium text-amber-700">
                                    Vista previa: las respuestas no se guardarán.
                                </p>
                            </div>
                        </div>

                        {{-- PREGUNTAS DEL FORMULARIO --}}
                        @forelse ($plantillaFormulario->preguntas as $indice => $pregunta)
                            @php
                                $tipo = $pregunta->tipoRespuesta?->nombre;
                                $escala = $pregunta->escala;
                            @endphp

                            <div wire:key="vista-pregunta-{{ $pregunta->id }}" x-data="{ respuesta: '', seleccionadas: [] }"
                                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">

                                {{-- NÚMERO Y PREGUNTA --}}
                                <div class="flex items-start gap-4">
                                    <x-numero-orden :numero="$indice + 1" />

                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-base font-bold leading-7 text-slate-900">
                                            {{ $pregunta->texto }}
                                        </h3>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $tipo ?? 'Sin tipo de respuesta' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- RESPUESTAS --}}
                                <div class="mt-6">

                                    {{-- ESCALA NUMÉRICA HORIZONTAL --}}
                                    {{-- ESCALA NUMÉRICA HORIZONTAL --}}
                                    @if ($tipo === 'Escala de 0 a 10' && $escala)
                                        <div class="space-y-3">

                                            <div class="flex flex-wrap items-center gap-2">
                                                @foreach (range($escala->minimo, $escala->maximo) as $numero)
                                                    <button type="button"
                                                        x-on:click="respuesta = '{{ $numero }}'"
                                                        :class="respuesta === '{{ $numero }}'
                                                            ?
                                                            'bg-blue-200 !text-blue-950 border-blue-700' :
                                                            'bg-blue-50 !text-blue-800 border-blue-200'"
                                                        class="flex h-10 w-10 items-center justify-center
                           rounded-lg border text-sm font-semibold
                           transition hover:border-blue-500">

                                                        {{ $numero }}

                                                    </button>
                                                @endforeach
                                            </div>

                                            {{-- OPCIONES ESPECIALES --}}
                                            <div class="flex flex-wrap gap-3">

                                                @if ($escala->permite_no_sabe)
                                                    <label
                                                        class="flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                                                        <input type="radio" name="escala-{{ $pregunta->id }}"
                                                            value="no_sabe" x-model="respuesta"
                                                            class="accent-blue-900" />
                                                        No sabe
                                                    </label>
                                                @endif

                                                @if ($escala->permite_no_aplica)
                                                    <label
                                                        class="flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                                                        <input type="radio" name="escala-{{ $pregunta->id }}"
                                                            value="no_aplica" x-model="respuesta"
                                                            class="accent-blue-900" />
                                                        No aplica
                                                    </label>
                                                @endif

                                            </div>
                                        </div>
                                        {{-- SELECCIÓN ÚNICA --}}
                                    @elseif ($tipo === 'Selección única')
                                        <div class="space-y-2">
                                            @foreach ($pregunta->opcionesRespuesta as $opcion)
                                                <label
                                                    class="flex cursor-pointer items-center gap-3
                                                rounded-xl border border-slate-200 p-3
                                                hover:border-blue-300 hover:bg-blue-50">

                                                    <input type="radio" name="unica-{{ $pregunta->id }}"
                                                        value="{{ $opcion->valor }}" x-model="respuesta"
                                                        class="h-4 w-4 accent-blue-900" />

                                                    <span class="text-sm text-slate-700">
                                                        {{ $opcion->etiqueta }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>

                                        {{-- SELECCIÓN MÚLTIPLE --}}
                                    @elseif ($tipo === 'Selección múltiple')
                                        <div class="space-y-2">
                                            @foreach ($pregunta->opcionesRespuesta as $opcion)
                                                <label
                                                    class="flex cursor-pointer items-center gap-3
                                                rounded-xl border border-slate-200 p-3
                                                hover:border-blue-300 hover:bg-blue-50">

                                                    <input type="checkbox" value="{{ $opcion->valor }}"
                                                        x-model="seleccionadas"
                                                        class="h-4 w-4 rounded accent-blue-900" />

                                                    <span class="text-sm text-slate-700">
                                                        {{ $opcion->etiqueta }}
                                                    </span>
                                                </label>
                                            @endforeach

                                            <p class="text-xs text-slate-400">
                                                Puede seleccionar varias opciones.
                                            </p>
                                        </div>

                                        {{-- TEXTO ABIERTO --}}
                                    @elseif ($tipo === 'Texto abierto')
                                        <flux:textarea x-model="respuesta" rows="4"
                                            placeholder="Escriba su respuesta aquí..." />
                                    @else
                                        <p class="text-sm text-amber-700">
                                            Esta pregunta no tiene una configuración válida.
                                        </p>
                                    @endif

                                </div>
                            </div>

                        @empty
                            <div class="rounded-xl bg-white p-8 text-center text-sm text-slate-500">
                                Esta plantilla todavía no tiene preguntas asignadas.
                            </div>
                        @endforelse

                        {{-- PIE DEL FORMULARIO --}}
                        @if ($plantillaFormulario->preguntas->isNotEmpty())
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs text-slate-500">
                                        Gracias por compartir su opinión.
                                    </p>

                                    <flux:button type="button" variant="primary" disabled>
                                        Enviar respuestas
                                    </flux:button>
                                </div>
                            </div>
                        @endif

                    </div>
                @endif
            </div>

            {{-- PIE FIJO DEL MODAL --}}
            <div class="flex shrink-0 items-center justify-between border-t border-slate-200 pt-4">
                <p class="text-xs text-slate-500">Vista previa de evaluación</p>

                <flux:button type="button" variant="primary" wire:click="cerrarFormulario">
                    Cerrar vista previa
                </flux:button>
            </div>

        </div>
    </flux:modal>
</div>
