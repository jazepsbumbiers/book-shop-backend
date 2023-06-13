<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'              => fake()->sentence(),
            'summary'           => fake()->text(),
            'rating'            => rand(0, 5),
            'price'             => self::generateRandomFloat(0.00, 100.00),
            'date_published'    => self::generateRandomDate('2020-01-01', '2023-06-10'),
        ];
    }
    
    private static function generateRandomFloat(float $minValue, float $maxValue): float
    {
        return $minValue + mt_rand() / mt_getrandmax() * ($maxValue - $minValue);
    }
    
    private static function generateRandomDate($startDate, $endDate): string
    {
        $min = strtotime($startDate);
        $max = strtotime($endDate);

        $value = rand($min, $max);

        return date('Y-m-d H:i:s', $value);
    }
}
