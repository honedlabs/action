<?php

declare(strict_types=1);

use Honed\Action\Concerns\HasAction;
use Honed\Action\Tests\Stubs\Product;

class HasActionTest
{
    use HasAction;

    public function execute($data)
    {
        //
    }
}

beforeEach(function () {
    $this->test = new HasActionTest;
    $this->fn = fn (Product $product) => $product;
});

it('sets', function () {
    expect($this->test)
        ->hasAction()->toBeFalse()
        ->getAction()->toBeNull()
        ->action($this->fn)->toBe($this->test)
        ->hasAction()->toBeTrue()
        ->getAction()->toBeInstanceOf(\Closure::class);
});

it('get parameters', function () {
    expect($this->test->getActionParameterNames(Product::query()))
        ->{0}->toBeInstanceOf(Product::class)
        ->{1}->toBe('product')
        ->{2}->toBe('products');

    expect($this->test->getActionParameterNames(product()))
        ->{0}->toBeInstanceOf(Product::class)
        ->{1}->toBe('product')
        ->{2}->toBe('products');
});
