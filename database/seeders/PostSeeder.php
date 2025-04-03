<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $judul = [
            'indonesia',
            'inggris',
            'jerman',
            'jepang',
        ];

        foreach($judul as $j){
            $slug = Str::slug($j);
            Post:: create([
                'title'=> $j,
               'slug' => Str::slug('Inggris') . '-' . Str::uuid(), // Use uuid for guaranteed uniqueness
                'description'=>'deskripsi untuk ' . $j,
                'content'=>'untuk'. $j,
                'status'=> 'published',
                'user_id'=>'1'                

            ]);
        }
        
    }
}
