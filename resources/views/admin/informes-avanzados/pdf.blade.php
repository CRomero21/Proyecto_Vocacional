
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h1>{{ $titulo }}</h1>
    
    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($datos as $item)
            <tr>
                @foreach($headers as $header)
                <td>
                    @php
                        $isArray = is_array($item);
                        $isObject = is_object($item);
                        
                        switch ($header) {
                            case 'Nombre':
                                echo $isArray ? ($item['name'] ?? '') : ($isObject ? ($item->name ?? '') : '');
                                break;
                            case 'Email':
                                echo $isArray ? ($item['email'] ?? '') : ($isObject ? ($item->email ?? '') : '');
                                break;
                            case 'Teléfono':
                                echo $isArray ? ($item['phone'] ?? '') : ($isObject ? ($item->phone ?? '') : '');
                                break;
                            case 'Género':
                                $sexo = $isArray ? ($item['sexo'] ?? '') : ($isObject ? ($item->sexo ?? '') : '');
                                echo strtolower($sexo) == 'm' ? 'Masculino' : (strtolower($sexo) == 'f' ? 'Femenino' : 'No especificado');
                                break;
                            case 'Departamento':
                                echo $isArray ? ($item['departamento'] ?? '') : ($isObject ? ($item->departamento ?? '') : '');
                                break;
                            case 'Ciudad':
                                echo $isArray ? ($item['ciudad'] ?? '') : ($isObject ? ($item->ciudad ?? '') : '');
                                break;
                            case 'Institución':
                                echo $isArray ? ($item['nombre'] ?? '') : ($isObject ? ($item->nombre ?? '') : '');
                                break;
                            case 'Total Estudiantes':
                                echo $isObject ? ($item->total_estudiantes ?? 0) : ($isArray ? ($item['total_estudiantes'] ?? 0) : 0);
                                break;
                            case 'Masculinos':
                                echo $isObject ? ($item->masculinos ?? 0) : ($isArray ? ($item['masculinos'] ?? 0) : 0);
                                break;
                            case 'Femeninos':
                                echo $isObject ? ($item->femeninos ?? 0) : ($isArray ? ($item['femeninos'] ?? 0) : 0);
                                break;
                            case 'Otros':
                                echo $isObject ? ($item->otros ?? 0) : ($isArray ? ($item['otros'] ?? 0) : 0);
                                break;
                            case 'Total Usuarios':
                                echo $isObject ? ($item->total ?? 0) : ($isArray ? ($item['total'] ?? 0) : 0);
                                break;
                            case 'Porcentaje':
                                $porcentaje = $isObject ? ($item->porcentaje ?? 0) : ($isArray ? ($item['porcentaje'] ?? 0) : 0);
                                echo $porcentaje . '%';
                                break;
                            case 'Estado':
                                echo $isArray ? ($item['estado'] ?? '') : '';
                                break;
                            case 'Cantidad':
                                echo $isArray ? ($item['cantidad'] ?? 0) : 0;
                                break;
                            case 'Tipo':
                                echo $isObject ? ($item->tipo_primario ?? '') : ($isArray ? ($item['tipo_primario'] ?? '') : '');
                                break;
                            case 'Descripción':
                                echo $isObject ? ($item->descripcion ?? '') : ($isArray ? ($item['descripcion'] ?? '') : '');
                                break;
                            case 'Total':
                                echo $isObject ? ($item->total ?? 0) : ($isArray ? ($item['total'] ?? 0) : 0);
                                break;
                            case 'Carrera':
                                echo $isObject ? ($item->nombre ?? '') : ($isArray ? ($item['nombre'] ?? '') : '');
                                break;
                            case 'Área':
                                echo $isObject ? ($item->area_conocimiento ?? '') : ($isArray ? ($item['area_conocimiento'] ?? '') : '');
                                break;
                            case 'Recomendaciones':
                                echo $isObject ? ($item->total ?? 0) : ($isArray ? ($item['total'] ?? 0) : 0);
                                break;
                            case 'Match Promedio':
                                $match = $isObject ? ($item->match_promedio ?? 0) : ($isArray ? ($item['match_promedio'] ?? 0) : 0);
                                echo $match . '%';
                                break;
                            default:
                                echo '';
                        }
                    @endphp
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>