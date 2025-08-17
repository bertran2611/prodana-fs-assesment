<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $cats = \App\Models\Category::pluck('id','name');
        \App\Models\Product::factory()->createMany([
            ['name'=>'Alpha Serum','price'=>149000,'stock'=>10,'category_id'=>$cats['Skincare'],'description'=>'AHA BHA'],
            ['name'=>'Velvet Lip','price'=>99000,'stock'=>25,'category_id'=>$cats['Makeup'],'description'=>'Matte']
        ]);
    }
}
