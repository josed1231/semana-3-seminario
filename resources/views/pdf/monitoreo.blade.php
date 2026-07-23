<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Monitoreo de Estudiantes - COTECNOVA</title>
    <style>
        @page {
            margin: 1.2cm 1cm 1.5cm 1cm;
        }
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 9px; 
            color: #1e293b; 
            margin: 0; 
            padding: 0; 
            line-height: 1.3;
        }
        .header { 
            text-align: center; 
            font-size: 15px; 
            font-weight: bold; 
            margin-bottom: 4px; 
            color: #0f172a; 
            text-transform: uppercase;
        }
        .subheader { 
            text-align: center; 
            font-size: 9.5px; 
            color: #64748b; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #004d2e;
            padding-bottom: 8px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 5px; 
        }
        th { 
            background-color: #004d2e; 
            color: #ffffff; 
            padding: 6px 8px; 
            font-size: 8.5px; 
            text-align: left; 
            text-transform: uppercase; 
            border: 1px solid #003822;
        }
        td { 
            border-bottom: 1px solid #e2e8f0; 
            padding: 6px 8px; 
            font-size: 8.5px; 
            vertical-align: middle;
        }
        tr:nth-child(even) { 
            background-color: #f8fafc; 
        }
        
        /* BADGES / INSIGNIAS */
        .badge { 
            padding: 2px 6px; 
            border-radius: 3px; 
            font-weight: bold; 
            font-size: 8px; 
            display: inline-block; 
            text-align: center;
        }
        .bg-danger { background-color: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }
        .bg-warning { background-color: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
        .bg-success { background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .bg-secondary { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        /* PIE DE PÁGINA FIX EN DOMPDF */
        .footer { 
            position: fixed;
            bottom: -0.8cm;
            left: 0;
            right: 0;
            text-align: center; 
            font-size: 8px; 
            color: #94a3b8; 
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        .page-number:before {
            content: "Página " counter(page);
        }
    </style>
</head>
<body>

    <div class="header">COTECNOVA - Reporte Institucional de Monitoreo</div>
    <div class="subheader">
        Generado el: {{ \Carbon\Carbon::now('America/Bogota')->format('d/m/Y h:i A') }} | 
        Generado por: {{ auth()->user()->name ?? auth()->user()->username ?? 'Sistema' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Código</th>
                <th style="width: 22%;">Estudiante</th>
                <th style="width: 20%;">Programa</th>
                <th style="width: 9%;">Jornada</th>
                <th style="width: 9%; text-align: center;">Riesgo</th>
                <th style="width: 15%;">Servicio / Orientación</th>
                <th style="width: 15%;">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($estudiantes as $e)
                <tr>
                    <td><strong>{{ $e->codigo_estudiante }}</strong></td>
                    <td>{{ $e->nombre_estudiante }}</td>
                    <td>{{ $e->programa->nombre_programa ?? 'Sin Asignar' }}</td>
                    <td>{{ ucfirst($e->jornada ?? 'N/A') }}</td>
                    <td style="text-align: center;">
                        @php
                            $nivel = $e->riesgo->nivel_riesgo ?? 'Sin evaluar';
                            $clase = match($nivel) {
                                'Alto' => 'bg-danger',
                                'Medio' => 'bg-warning',
                                'Bajo' => 'bg-success',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $clase }}">{{ $nivel }}</span>
                    </td>
                    <td>{{ $e->orientacionPsicologica->nivel_servicio ?? 'N/A' }}</td>
                    <td>{{ $e->orientacionPsicologica->observaciones ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px; color: #64748b;">
                        No se encontraron registros con los filtros seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Sistema de Información para la Detección Temprana de la Deserción Estudiantil - COTECNOVA | <span class="page-number"></span>
    </div>

</body>
</html>