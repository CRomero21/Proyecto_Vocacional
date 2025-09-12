
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 30px; color: #222; position: relative; }
        .logo-institucional {
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: auto;
            margin-top: -35px;
            margin-right: 10px;
        }
        h1 {
            color: #2a4365;
            font-size: 26px;
            margin: 0 0 10px 0;
            padding: 0;
            border-bottom: 2px solid #2a4365;
            padding-bottom: 4px;
            text-align: center;
        }
        .subtitulo { color: #64748b; font-size: 16px; margin-bottom: 18px; text-align: center; }
        h2 { color: #2b6cb0; margin-top: 28px; margin-bottom: 10px; }
        .info { margin-bottom: 18px; }
        .info strong { color: #2b6cb0; }
        .perfil-list { margin-bottom: 18px; }
        .perfil-list li { margin-bottom: 5px; font-size: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 13px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 4px; text-align: left; }
        th { background-color: #e2e8f0; color: #2a4365; font-size: 12px; }
        tr.principal { background: #e0f2fe; }
        tr:nth-child(even):not(.principal) { background-color: #f8fafc; }
        .universidad-list { margin: 0; padding-left: 14px; }
        .universidad-list li { font-size: 12px; margin-bottom: 2px; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 5px; background: #bee3f8; color: #2b6cb0; font-size: 10px; margin-left: 3px;}
        .match { font-weight: bold; color: #2563eb; font-size: 12px; }
        .sec { color: #64748b; font-size: 11px; }
        .duracion { font-size: 10px; color: #475569; margin-left: 4px; }
        .desc { font-size: 11px; color: #444; margin-top: 2px; }
        .footer { margin-top: 24px; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 8px; text-align: center; }
    </style>
</head>
<body>
    <img src="{{ public_path('images/LOGO_UNO.png') }}" alt="Logo institucional" class="logo-institucional">
    <h1>{{ $titulo }}</h1>
    <div class="subtitulo">Reporte personalizado de orientación vocacional</div>
    <div class="info">
        <strong>Estudiante:</strong> {{ $test->user->name ?? 'N/A' }}<br>
        <strong>Fecha:</strong>
        {{ \Carbon\Carbon::parse($test->fecha_completado)->setTimezone('America/La_Paz')->format('d/m/Y H:i') }} <span class="sec"></span>
    </div>

    <h2>Tu perfil RIASEC</h2>
    <ul class="perfil-list">
        @foreach(($resultados['porcentajes'] ?? []) as $tipo => $valor)
            <li>
                <span class="badge">{{ $tipo }}</span>
                <strong>{{ $valor }}%</strong>
                <span class="sec">{{ $tiposPersonalidad[$tipo] ?? '' }}</span>
            </li>
        @endforeach
    </ul>

    <h2>Carreras recomendadas</h2>
    <table>
        <thead>
            <tr>
                <th>Carrera</th>
                <th>Área</th>
                <th>Match</th>
                <th>Universidades</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($resultados['recomendaciones'] ?? []) as $carrera)
                <tr @if(!empty($carrera['es_primaria'])) class="principal" @endif>
                    <td>
                        <strong>{{ $carrera['nombre'] ?? '' }}</strong>
                        @if(!empty($carrera['es_primaria']))
                            <span class="badge">Principal</span>
                        @endif
                        @if(!empty($carrera['duracion']))
                            <span class="duracion">({{ $carrera['duracion'] }})</span>
                        @endif
                        <div class="desc">{{ $carrera['descripcion'] ?? '-' }}</div>
                    </td>
                    <td>{{ $carrera['area'] ?? '-' }}</td>
                    <td class="match">{{ $carrera['match'] ?? '0' }}%</td>
                    <td>
                        @if(!empty($carrera['universidades']))
                            <ul class="universidad-list">
                                @foreach($carrera['universidades'] as $uni)
                                    <li>
                                        {{ is_array($uni) ? $uni['nombre'] : $uni->nombre }}
                                        @php
                                            $duracion = is_array($uni) ? ($uni['duracion'] ?? null) : ($uni->duracion ?? null);
                                        @endphp
                                        @if($duracion)
                                            <span class="duracion">{{ $duracion }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="sec">Sin universidades registradas</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center; color:#888;">No se encontraron recomendaciones para tu perfil.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">
        Este informe fue generado automáticamente por el sistema de orientación vocacional.<br>
        Para una mejor interpretación de tus resultados, consulta con un orientador profesional.<br>
        Fecha de generación: {{ now('America/La_Paz')->format('d/m/Y H:i') }} (hora Bolivia)
    </div>
</body>
</html>