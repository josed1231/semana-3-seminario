@extends('layouts.app')

@section('titulo', 'Listado de Tareas')
@section('titulo_pagina', 'Listado de Tareas')

@section('contenido')
    <div class="row mb-3">
        <div class="col-md-6">
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nueva Tarea
            </a>
        </div>
        <div class="col-md-6">
            <form method="GET" action="{{ route('tasks.index') }}" class="row g-2">
                <div class="col">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar tarea..." value="{{ request('buscar') }}">
                </div>
                <div class="col">
                    <select name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado')=='pendiente'?'selected':'' }}>Pendiente</option>
                        <option value="en_progreso" {{ request('estado')=='en_progreso'?'selected':'' }}>En Progreso</option>
                        <option value="completada" {{ request('estado')=='completada'?'selected':'' }}>Completada</option>
                    </select>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-outline-secondary">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Estado</th>
                        <th>Categoría</th>
                        <th>Fecha Límite</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tareas as $tarea)
                        <tr>
                            <td>{{ $tarea->id }}</td>
                            <td>{{ $tarea->titulo }}</td>
                            <td>
                                @php
                                    $color = $tarea->estado == 'completada' ? 'success' : ($tarea->estado == 'en_progreso' ? 'warning' : 'secondary');
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ $tarea->estado }}</span>
                            </td>
                            <td>{{ $tarea->category->nombre ?? 'Sin categoría' }}</td>
                            <td>{{ $tarea->fecha_limite ?? 'No definida' }}</td>
                            <td>
                                <a href="{{ route('tasks.edit', $tarea->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('tasks.destroy', $tarea->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta tarea?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay tareas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection