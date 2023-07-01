<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Config;
use App\Models\User;


class CreateConfig extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $config1 = Config::create([
            'name' => 'sharpcontrast', 
            'value' => 'false',
            'title' => 'Alto contraste', 
            'description' => 'Cambia los colores a un contraste más alto',
            'domain' => 'True/False'
        ]);
        
        $config2 = Config::create([
            'name' => 'tts', 
            'value' => 'false',
            'title' => 'Texto a voz', 
            'description' => 'Lee en voz alta todos los textos',
            'domain' => 'True/False'
        ]);

        foreach (User::all() as $user) {
            $user->configs()->attach($config1->id, ['value' => 'true', 'description' => 'Descripción']);
            if($user->name !== "Administrador")
                $user->configs()->attach($config2->id, ['value' => 'true', 'description' => 'Descripción']);
        }   
    }
}
