<?php

namespace Database\Factories;

use App\Models\Pack;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pack::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = array_keys(config('packs.types'));
        
        return [
            'nom' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->paragraph,
            'type' => $this->faker->randomElement($types),
            'prix' => $this->faker->randomFloat(2, 100, 5000),
            'prix_promo' => $this->faker->optional(0.3)->randomFloat(2, 50, 4000),
            'duree_jours' => $this->faker->numberBetween(30, 365),
            'est_actif' => $this->faker->boolean(80), // 80% de chance d'être actif
            'est_populaire' => $this->faker->boolean(20), // 20% de chance d'être populaire
        ];
    }

    /**
     * Indicate that the pack is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => true,
        ]);
    }

    /**
     * Indicate that the pack is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }

    /**
     * Indicate that the pack is popular.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_populaire' => true,
        ]);
    }

    /**
     * Indicate that the pack has a specific type.
     */
    public function type(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }

    /**
     * Indicate that the pack has a discount.
     */
    public function withDiscount(float $discountPercent = 20): static
    {
        return $this->state(function (array $attributes) use ($discountPercent) {
            $discount = $attributes['prix'] * ($discountPercent / 100);
            return [
                'prix_promo' => $attributes['prix'] - $discount,
            ];
        });
    }
}
