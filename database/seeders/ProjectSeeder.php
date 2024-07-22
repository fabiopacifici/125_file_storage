<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {



        for ($i = 0; $i < 10; $i++) {

            $project = new Project();
            $project->name = $faker->sentence(3);
            $project->slug = Str::slug($project->name, '-');
            $project->description = $faker->sentence();
            $project->cover_image = $faker->imageUrl(600, 400, 'projects', true, gray: true, format: 'jpg');
            $project->save();
        }
    }
}
