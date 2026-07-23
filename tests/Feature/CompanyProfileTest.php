<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\CompanyProfileController;
use Tests\TestCase;

class CompanyProfileTest extends TestCase
{
    public function test_it_defines_the_four_current_henna_colors(): void
    {
        $controller = new CompanyProfileController();
        $method = new \ReflectionMethod($controller, 'categories');
        $method->setAccessible(true);

        $categories = $method->invoke($controller);

        $this->assertSame(
            ['white', 'nude-semi-gold', 'maroon', 'pink-rose'],
            array_column($categories, 'id'),
        );
        $this->assertSame(
            ['White Henna', 'Nude Semi Gold Henna', 'Henna Maroon', 'Pink Rose Henna'],
            array_column($categories, 'name'),
        );
    }
}
