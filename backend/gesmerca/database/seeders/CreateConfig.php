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
            switch ($user->name) {
                case "Administrador":
                    $user->configs()->attach($config1->id, ['value' => 'false', 'description' => 'Alto contraste']);
                    $user->configs()->attach($config2->id, ['value' => 'false', 'description' => 'Texto a voz']);
                    break;
                case "Empleado":
                    $user->configs()->attach($config1->id, ['value' => 'true', 'description' => 'Alto contraste']);
                    $user->configs()->attach($config2->id, ['value' => 'false', 'description' => 'Texto a voz']);
                    break;
                case "Usuario":
                    $user->configs()->attach($config1->id, ['value' => 'true', 'description' => 'Alto contraste']);
                    $user->configs()->attach($config2->id, ['value' => 'true', 'description' => 'Texto a voz']);
                    break;
            }
        }   
    }
}
