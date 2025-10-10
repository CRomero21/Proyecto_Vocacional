<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Testing User count... ";
    $count = App\Models\User::where('role', 'estudiante')->count();
    echo $count . PHP_EOL;

    echo "Testing Test count... ";
    $testCount = App\Models\Test::where('completado', true)->count();
    echo $testCount . PHP_EOL;

    echo "Testing Test::with... ";
    $tests = App\Models\Test::with('user')->latest()->take(5)->get();
    echo 'Found ' . $tests->count() . ' tests' . PHP_EOL;

    echo "Testing DB query for trends... ";
    $fechaHaceUnMes = \Carbon\Carbon::now()->subDays(30);
    $tendenciasTests = \Illuminate\Support\Facades\DB::table('tests')
        ->select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as fecha, COUNT(*) as total'))
        ->where('created_at', '>=', $fechaHaceUnMes)
        ->groupBy('fecha')
        ->orderBy('fecha')
        ->get();
    echo 'Found ' . $tendenciasTests->count() . ' trend records' . PHP_EOL;

    echo "All queries successful!" . PHP_EOL;

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
}