<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\RestoreProduct;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->action = new RestoreProduct();
});

it('restores a single soft deleted model', function () {
    $product = Product::factory()->create();
    
    $product->delete();
    
    $this->assertTrue($product->trashed());
    
    $result = $this->action->handle($product);
    
    $this->assertInstanceOf(Product::class, $result);
    $this->assertFalse($result->trashed());
    $this->assertEquals($product->id, $result->id);
    
    // Verify it's restored in the database
    $product->refresh();
    $this->assertFalse($product->trashed());
});

it('restores multiple soft deleted models using query builder', function () {
    $products = Product::factory()->count(3)->create();
    
    // Soft delete all products
    $products->each->delete();
    
    // Verify all are soft deleted
    $products->each(fn ($product) => $this->assertTrue($product->fresh()->trashed()));
    
    // Get a query builder for only trashed products
    $query = Product::onlyTrashed();
    
    $result = $this->action->handle($query);
    
    $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    $this->assertCount(3, $result);
    
    // Verify all products in the result are restored
    $result->each(function ($product) {
        $this->assertInstanceOf(Product::class, $product);
        $this->assertFalse($product->trashed());
    });
    
    // Verify all products are restored in the database
    $products->each(function ($product) {
        $this->assertFalse($product->fresh()->trashed());
    });
});

it('restores multiple soft deleted models using scoped query builder', function () {
    // Create some products, some will be deleted
    $productsToDelete = Product::factory()->count(2)->create();
    $productsToKeep = Product::factory()->count(2)->create();
    
    // Soft delete only some products
    $productsToDelete->each->delete();
    
    // Verify correct products are soft deleted
    $productsToDelete->each(fn ($product) => $this->assertTrue($product->fresh()->trashed()));
    $productsToKeep->each(fn ($product) => $this->assertFalse($product->fresh()->trashed()));
    
    // Get a query builder for only the trashed products
    $query = Product::onlyTrashed()->whereIn('id', $productsToDelete->pluck('id'));
    
    $result = $this->action->handle($query);
    
    $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    $this->assertCount(2, $result);
    
    // Verify only the intended products were restored
    $productsToDelete->each(function ($product) {
        $this->assertFalse($product->fresh()->trashed());
    });
    
    // Verify other products remained untouched
    $productsToKeep->each(function ($product) {
        $this->assertFalse($product->fresh()->trashed());
    });
});

it('returns empty collection when restoring query with no trashed models', function () {
    // Create some products but don't delete them
    Product::factory()->count(2)->create();
    
    // Try to restore from onlyTrashed query (should be empty)
    $query = Product::onlyTrashed();
    
    $result = $this->action->handle($query);
    
    $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    $this->assertCount(0, $result);
});

it('handles restoring already restored model', function () {
    $product = Product::factory()->create();
    
    // Product is not trashed initially
    $this->assertFalse($product->trashed());
    
    // Delete and then restore
    $product->delete();
    $product->restore();
    
    $this->assertFalse($product->fresh()->trashed());
    
    // Try to restore again (should handle gracefully)
    $result = $this->action->handle($product);
    
    $this->assertInstanceOf(Product::class, $result);
    $this->assertFalse($result->trashed());
});
