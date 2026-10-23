<?php

namespace Toneflix\ApprovableNotifications\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toneflix\ApprovableNotifications\Tests\Models\School;

class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'address' => $this->faker->streetAddress,
        ];
    }
}
