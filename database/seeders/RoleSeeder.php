<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $roles = [
            ['name' => 'Administrador', 'description' => 'Gestionar todo el sistema'],
            ['name'=> 'Vendedor', 'description'=> 'Se encarga de las Ventas'],
            ['name'=> 'Comprador', 'description'=> 'Se encarga de las Compras'],
            ['name'=> 'Mantenedor', 'description'=> 'Responsable del Mantenimiento de datos'],
            ['name'=> 'Analista', 'description'=> 'Responsable de Analizar Datos'],
            ['name'=> 'Almacen', 'description'=> 'Gestiona Inventarios'],
        ];

        foreach ($roles as $role)
        {
            Role::create($role);
        }
    }
}
