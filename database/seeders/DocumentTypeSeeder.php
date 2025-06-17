<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $documentTypes = [
            [
                'name' => 'Factura',
                'description' => 'Documento que respalda una transacción de venta, detallando productos o servicios vendidos.',
                'type' => 'sale'
            ],
            [
                'name' => 'Boleta',
                'description' => 'Documento simplificado que respalda la venta de productos o servicios a consumidores finales.',
                'type' => 'sale'
            ],
            [
                'name' => 'Guía de Remisión',
                'description' => 'Documento que autoriza el traslado de mercancías, usado en transporte y entregas de productos.',
                'type' => 'purchase'
            ]
        ];

        foreach ($documentTypes as $documentType)
        {
            DocumentType::create($documentType);
        }
    }
}
