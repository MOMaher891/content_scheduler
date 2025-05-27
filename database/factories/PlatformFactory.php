<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Platform>
 */
class PlatformFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $platforms = [
            'facebook',
            'twitter',
            'instagram',
            'linkedin',
            'tiktok',
            'youtube',
            'pinterest',
            'reddit',
            'tumblr',
            'snapchat',
            'threads',
            'telegram',
            'whatsapp',
            'mastodon',
            'medium',
        ];
        return [
            'name' => fake()->randomElement($platforms),
        ];
    }
}

