<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;

class ProductListingTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/products';

    public function test_basic_product_listing(): void
    {
        $products = Product::factory()->count(10)->create();
        $response = $this->get($this->url);

        $response->assertStatus(200)
        ->assertJson([
            'message' => 'Listing Successful',
        ])
        ->assertJsonCount($products->count(), 'data.*');
    }

    public function test_negative_min_price_param(): void
    {
        $response = $this->call('GET', $this->url, ['min_price' => -5]);

        $response->assertStatus(422)
        ->assertJson([
            'message' => 'Validation Errors',
        ]);
    }

    public function test_negative_max_price_param(): void
    {
        $response = $this->call('GET', $this->url, ['max_price' => -5]);

        $response->assertStatus(422)
        ->assertJson([
            'message' => 'Validation Errors',
        ]);
    }

    public function test_min_price_param_greater_than_max_price_param(): void
    {
        $response = $this->call('GET', $this->url, ['min_price' => 10, 'max_price' => 5]);

        $response->assertStatus(422)
        ->assertJson([
            'message' => 'Validation Errors',
        ]);
    }

    public function test_name_param(): void
    {
        $products = Product::factory()->count(20)->create();
        $search_term = 'em';

        $filtered_products = $products->filter(function ($product) use ($search_term) {
            preg_match("/.*{$search_term}.*/", $product->name, $matches);

            if (empty($matches)) {
                return false;
            }

            return true;
        });

        $response = $this->call('GET', $this->url, ['name' => $search_term]);

        $response->assertStatus(200)
        ->assertJson([
            'message' => 'Listing Successful',
        ])
        ->assertJsonCount($filtered_products->count(), 'data.*');
    }

    public function test_category_param(): void
    {
        $products = Product::factory()->definedCategories()->count(20)->create();
        $search_term = 'Foods';

        $foods = $products->filter(function ($product) use ($search_term) {
            return $product->category == $search_term;
        });

        $response = $this->call('GET', $this->url, ['category' => $search_term]);

        $response->assertStatus(200)
        ->assertJson([
            'message' => 'Listing Successful',
        ])
        ->assertJsonCount($foods->count(), 'data.*');
    }

    public function test_category_param_no_match(): void
    {
        $products = Product::factory()->definedCategories()->count(20)->create();
        $search_term = 'Food';

        $foods = $products->filter(function ($product) use ($search_term) {
            return $product->category == $search_term;
        });

        $response = $this->call('GET', $this->url, ['category' => $search_term]);

        $response->assertStatus(200)
        ->assertJson([
            'message' => 'Listing Successful',
        ])
        ->assertJsonCount($foods->count(), 'data.*');
    }

    public function test_combination_params(): void
    {
        $products = Product::factory()->definedCategories()->count(20)->create();
        $name = 'em';
        $category = 'Foods';
        $min_price = 12.35;
        $max_price = 30;

        $filtered_products = $products->filter(function ($product) use ($name) {
            preg_match("/.*{$name}.*/", $product->name, $matches);

            if (empty($matches)) {
                return false;
            }

            return true;
        })
        ->filter(function ($product) use ($category) {
            return $product->category == $category;
        })
        ->filter(function ($product) use ($min_price) {
            return $product->price >= $min_price;
        })
        ->filter(function ($product) use ($max_price) {
            return $product->price <= $max_price;
        });

        $response = $this->call('GET', $this->url, [
            'name' => $name,
            'category' => $category,
            'min_price' => $min_price,
            'max_price' => $max_price,
        ]);

        $response->assertStatus(200)
        ->assertJson([
            'message' => 'Listing Successful',
        ])
        ->assertJsonCount($filtered_products->count(), 'data.*');
    }
}
