<?php

use Illuminate\Database\Seeder;
use App\Models\Paquete;
use App\Models\Producto;

class PaqueteSeeder extends Seeder
{
    public function run()
    {
        // Crear el paquete "TORRE DE LAPAROSCOPIA STRYKER 1588"
        $paquete = Paquete::create(['nombre' => 'TORRE DE LAPAROSCOPIA STRYKER 1588']);

        // IDs de los productos a asociar
        $productosIds = [
            190, // CAMARA CON CABEZAL AIM 1588
            75,  // Fuente de luz con fibra verde L10
            48,  // Insuflador c/manguera y yugo 45l Pneumosure X
            35,  // Lente 10mm 30°
            70,  // Grabador SDC3
            24,  // Monitor Vision Pro Led DE 32 pulgadas
            71   // Video Carro
        ];

        // Asociar los productos al paquete si existen en la base de datos
        $productos = Producto::whereIn('id', $productosIds)->get();

        if ($productos->isEmpty()) {
            echo "No se encontraron los productos para la torre de laparoscopía.\n";
            return;
        }

        $paquete->productos()->attach($productos->pluck('id')->toArray());

        // Confirmación en la consola
        echo "Paquete '{$paquete->nombre}' asociado con productos: " . $productos->pluck('tipo_equipo')->implode(', ') . "\n";
    }
}
