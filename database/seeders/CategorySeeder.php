<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $categories = [
            ['name' => 'Vodka', 'description' => 'Líquido destilado a base de cereales o patatas'],
            ['name' => 'Ron', 'description' => 'Bebida alcohólica elaborada a partir de la caña de azúcar o melaza'],
            ['name' => 'Tequila', 'description' => 'Destilado de agave originario de México'],
            ['name' => 'Whisky', 'description' => 'Bebida alcohólica destilada de cebada malteada'],
            ['name' => 'Ginebra', 'description' => 'Bebida alcohólica a base de bayas de enebro y otros ingredientes botánicos'],
            ['name' => 'Licor', 'description' => 'Bebida alcohólica que puede ser dulce o amargosa, elaborada con frutas, hierbas, o especias']
        ];

        foreach ($categories as $category)
        {
            Category::create($category);
        }
    }
}
