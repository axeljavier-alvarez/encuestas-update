<div class="mx-auto w-full max-w-[1600px] px-2 sm:px-4 lg:px-6 space-y-5">

    {{-- ENCABEZADO --}}
    <div class="overflow-hidden rounded-2xl px-5 py-5 text-white shadow-lg sm:px-6"
        style="background: linear-gradient(110deg, #020617 0%, #172554 50%, #312e81 100%);">

        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex min-w-0 items-center gap-4">

                <img src="{{ asset('images/icono_muni.png') }}" alt="Municipalidad de Guatemala" width="75"
                    height="75" class="shrink-0 object-contain"
                    style="width:75px;height:75px;max-width:75px;max-height:75px;" />

                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-blue-200">
                        Administración
                    </p>

                    <h1 class="mt-1 text-xl font-black tracking-tight sm:text-2xl">
                        Plantillas de evaluación
                    </h1>

                    <p class="mt-1.5 text-xs leading-5 text-blue-100 sm:text-sm">
                        Cree y administre las plantillas de evaluación
                        para las encuestas de satisfacción.
                    </p>
                </div>
            </div>

            <flux:button type="button" wire:click="crearPlantilla" variant="primary" icon="plus"
                class="shrink-0 font-bold"
                style="background-color:#FFFFFF !important;
                       color:#1E3A8A !important;
                       border:1px solid #FFFFFF !important;">
                Nueva plantilla
            </flux:button>
        </div>
    </div>

    {{-- MENSAJE --}}
    @if (session('mensaje'))
        <div x-data="{ mostrar: true }" x-show="mostrar" x-init="setTimeout(() => mostrar = false, 4000)"
            class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
            {{ session('mensaje') }}
        </div>
    @endif

    {{-- CONTENIDO --}}
    <div class="space-y-5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">

        {{-- TÍTULO Y BÚSQUEDA --}}
        <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-end sm:justify-between">

            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-700">
                    Formularios disponibles
                </p>

                <h2 class="mt-1 text-xl font-black tracking-tight text-slate-950">
                    Listado de plantillas
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Consulte y edite la información general de cada plantilla.
                </p>
            </div>

            <div class="w-full sm:w-80">
                <flux:input wire:model.live.debounce.300ms="buscar" label="Buscar plantilla"
                    placeholder="Código o nombre..." icon="magnifying-glass" clearable />
            </div>
        </div>

        {{-- TARJETAS DE RESUMEN --}}
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">

            {{-- TOTAL --}}
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-blue-700">
                            Total de plantillas
                        </p>
                        <p class="mt-2 text-3xl font-black text-blue-950">
                            {{ $totalPlantillas }}
                        </p>
                        <p class="mt-1 text-xs text-blue-700">
                            Plantillas registradas
                        </p>
                    </div>

                    <div class="rounded-xl bg-blue-100 p-3">
                        <flux:icon.document-text class="size-7 text-blue-700" />
                    </div>
                </div>
            </div>

            {{-- ACTIVAS --}}
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">
                            Plantillas activas
                        </p>
                        <p class="mt-2 text-3xl font-black text-emerald-900">
                            {{ $totalActivas }}
                        </p>
                        <p class="mt-1 text-xs text-emerald-700">
                            Actualmente habilitadas
                        </p>
                    </div>

                    <div class="rounded-xl bg-emerald-100 p-3">
                        <flux:icon.check-circle class="size-7 text-emerald-700" />
                    </div>
                </div>
            </div>

            {{-- INACTIVAS --}}
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-red-700">
                            Plantillas inactivas
                        </p>
                        <p class="mt-2 text-3xl font-black text-red-900">
                            {{ $totalInactivas }}
                        </p>
                        <p class="mt-1 text-xs text-red-700">
                            Actualmente deshabilitadas
                        </p>
                    </div>

                    <div class="rounded-xl bg-red-100 p-3">
                        <flux:icon.x-circle class="size-7 text-red-700" />
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLA DE PLANTILLAS --}}
        <flux:table :paginate="$plantillas">

            <flux:table.columns>
                <flux:table.column>Código</flux:table.column>
                <flux:table.column>Plantilla</flux:table.column>
                <flux:table.column class="text-center">Estado</flux:table.column>
                <flux:table.column class="text-end">Acciones</flux:table.column>
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

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $plantilla->descripcion ?: 'Sin descripción registrada.' }}
                            </p>
                        </flux:table.cell>

                        <flux:table.cell class="text-center">
                            <flux:badge variant="pill" :color="$plantilla->activo ? 'emerald' : 'red'" size="sm">
                                {{ $plantilla->activo ? 'Activa' : 'Inactiva' }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell class="text-end">
                            <div class="flex flex-wrap items-center justify-end gap-2">

                                {{-- EDITAR --}}
                                <flux:button type="button" size="sm" variant="subtle" icon="pencil-square"
                                    wire:click="editar({{ $plantilla->id }})">
                                    Editar
                                </flux:button>

                                {{-- ACTIVAR / DESACTIVAR --}}
                                <flux:button type="button" size="sm"
                                    :variant="$plantilla->activo ? 'danger' : 'primary'"
                                    :icon="$plantilla->activo ? 'x-circle' : 'check-circle'"
                                    wire:click="confirmarCambioEstado({{ $plantilla->id }})"
                                    style="width: 135px !important;
                                    min-width: 135px !important;
                                    max-width: 135px !important;
                                    display: inline-flex !important;
                                    align-items: center !important;
                                    justify-content: center !important;
                                    box-sizing: border-box !important;">

                                    {{ $plantilla->activo ? 'Desactivar' : 'Activar' }}

                                </flux:button>

                            </div>
                        </flux:table.cell>


                    </flux:table.row>

                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="py-10 text-center">
                            <p class="font-semibold text-slate-700">
                                No se encontraron plantillas.
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Pruebe otra búsqueda o registre una nueva plantilla.
                            </p>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>

        </flux:table>

    </div>

    {{-- MODAL CREAR / EDITAR --}}
    <flux:modal wire:model="mostrarModalPlantilla" class="max-w-lg space-y-6">

        <div>
            <flux:heading size="lg">
                {{ $plantillaId ? 'Editar plantilla' : 'Nueva plantilla' }}
            </flux:heading>

            <flux:subheading>
                Complete los datos generales de la plantilla de evaluación.
            </flux:subheading>
        </div>

        <form wire:submit="guardarPlantilla" class="space-y-4">

            <flux:input wire:model="codigoPlantilla" label="Código" placeholder="Ej. CURSO-01" maxlength="10" />

            @error('codigoPlantilla')
                <p class="text-xs font-bold text-red-600">{{ $message }}</p>
            @enderror

            <flux:input wire:model="nombrePlantilla" label="Nombre" placeholder="Nombre de la plantilla" />

            @error('nombrePlantilla')
                <p class="text-xs font-bold text-red-600">{{ $message }}</p>
            @enderror

            <flux:textarea wire:model="descripcionPlantilla" label="Descripción" rows="3"
                placeholder="Descripción de la plantilla..." />

            @error('descripcionPlantilla')
                <p class="text-xs font-bold text-red-600">{{ $message }}</p>
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
    <x-confirmacion nombre="confirmar-estado-plantilla" :titulo="$nuevoEstadoPlantilla ? 'Activar plantilla' : 'Desactivar plantilla'" :mensaje="$nuevoEstadoPlantilla
        ? '¿Está seguro de que desea activar la plantilla ' . $nombrePlantillaEstado . '?'
        : '¿Está seguro de que desea desactivar la plantilla ' . $nombrePlantillaEstado . '?'" :texto-confirmar="$nuevoEstadoPlantilla ? 'Sí, activar' : 'Sí, desactivar'"
        :color="$nuevoEstadoPlantilla ? 'primary' : 'danger'" accion="cambiarEstadoPlantilla">
    </x-confirmacion>
</div>
