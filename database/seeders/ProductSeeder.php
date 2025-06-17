<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $categories = Category::all();

       $users = User::whereHas('role', fn($query) => $query->where('name', 'Administrador')->orWhere('name', 'Mantenedor'))->get();

       $products = [
        ['name' => 'Licor', 'description' => 'Bebida alcohólica que puede ser dulce o amargosa, elaborada con frutas, hierbas, o especias', 'price' => 59900, 'quantity' => 100],
        ['name' => 'Vodka', 'description' => 'Líquido destilado a base de cereales o patatas, generalmente con un contenido alcohólico elevado', 'price' => 85000, 'quantity' => 50],
        ['name' => 'Ron', 'description' => 'Bebida alcohólica elaborada a partir de la caña de azúcar o melaza', 'price' => 70000, 'quantity' => 75],
        ['name' => 'Tequila', 'description' => 'Destilado de agave originario de México, generalmente servido en shots o cócteles', 'price' => 95000, 'quantity' => 60],
        ['name' => 'Whisky', 'description' => 'Bebida alcohólica destilada de cebada malteada, con sabor ahumado y de envejecimiento en barricas de roble', 'price' => 115000, 'quantity' => 40],
        ['name' => 'Ginebra', 'description' => 'Bebida alcohólica a base de bayas de enebro y otros ingredientes botánicos', 'price' => 78000, 'quantity' => 90],
        ['name' => 'Vino tinto', 'description' => 'Vino elaborado a partir de uvas rojas o negras, envejecido para dar un sabor robusto', 'price' => 40000, 'quantity' => 150],
        ['name' => 'Vino blanco', 'description' => 'Vino elaborado a partir de uvas verdes o amarillas, con un sabor más suave y afrutado', 'price' => 42000, 'quantity' => 130],
        ['name' => 'Champán', 'description' => 'Vino espumoso originario de la región de Champagne en Francia, conocido por su burbujeo y frescura', 'price' => 175000, 'quantity' => 30],
        ['name' => 'Brandy', 'description' => 'Bebida alcohólica destilada de vino o de otras frutas, conocida por su sabor suave y afrutado', 'price' => 95000, 'quantity' => 50],
        ['name' => 'Cerveza', 'description' => 'Bebida alcohólica elaborada a partir de cebada, lúpulo, agua y levadura', 'price' => 2500, 'quantity' => 200],
        ['name' => 'Aguardiente', 'description' => 'Bebida alcohólica destilada generalmente a partir de caña de azúcar o frutas', 'price' => 32000, 'quantity' => 80],
        ['name' => 'Pisco', 'description' => 'Destilado de uvas, originario de Perú y Chile, utilizado en cócteles o consumido solo', 'price' => 90000, 'quantity' => 60],
        ['name' => 'Absinthe', 'description' => 'Licor de hierbas, conocido por su alto contenido alcohólico y sabor anisado', 'price' => 145000, 'quantity' => 20],
        ['name' => 'Coñac', 'description' => 'Brandy de vino originario de la región de Cognac en Francia, con un sabor complejo y envejecido', 'price' => 130000, 'quantity' => 45],
        ['name' => 'Cider', 'description' => 'Bebida alcohólica elaborada a partir de la fermentación de manzanas', 'price' => 18000, 'quantity' => 60],
        ['name' => 'Mezcal', 'description' => 'Destilado de agave originario de México, similar al tequila pero con un sabor más ahumado', 'price' => 85000, 'quantity' => 35],
        ['name' => 'Sangría', 'description' => 'Bebida alcohólica a base de vino, frutas y azúcar, típicamente servida fría', 'price' => 32000, 'quantity' => 50],
        ['name' => 'Fernet', 'description' => 'Licor amargo, hecho a base de hierbas, especias y raíces, comúnmente mezclado con cola', 'price' => 50000, 'quantity' => 40],
        ['name' => 'Vino espumoso', 'description' => 'Vino con gas natural, similar al champán, pero con menor precio y disponible en varias variedades', 'price' => 42000, 'quantity' => 80],
       ];

       foreach($products as $product)
       {
            Product::create([
                'name'=> $product['name'],
                'description'=> $product['description'],
                'price'=> $product['price'],
                'quantity'=> $product['quantity'],
                'category_id'=> $categories->random()->id,
                'user_id' => $users->random()->id,
                'status' => 'available',
            ]);
       }
    }
}
