<?php

declare(strict_types=1);

use Honed\Action\Batch;
use Honed\Action\Tests\Stubs\ProductActions;

// beforeEach(function () {
//     $this->test = Batch::make('test');
//     $this->group = ProductActions::make();
// });

// it('has primitive', function () {
//     expect($this->test)
//         ->primitive()->toBe(Batch::class);

//     expect($this->group)
//         ->primitive()->toBe(Batch::class);
// });

// it('has route key', function () {
//     $key = $this->test->getRouteKey();

//     expect($this->test)
//         ->decode($key)->toBe(Batch::class);

//     $key = $this->group->getRouteKey();

//     expect($this->group)
//         ->decode($key)->toBe(ProductActions::class);
// });

// it('resolves route binding', function () {
//     $id = $this->test->getRouteKey();

//     // Not a subclass
//     expect($this->test)
//         ->resolveRouteBinding($id)->toBeNull();

//     $id = $this->group->getRouteKey();

//     expect($this->group)
//         ->resolveRouteBinding($id)->toBeInstanceOf(ProductActions::class);

//     expect(User::factory()->create())
//         ->resolveRouteBinding($id)->toBeNull();
// });

// it('resolves route child binding', function () {
//     $id = $this->test->getRouteKey();

//     // Not a subclass
//     expect($this->test)
//         ->resolveChildRouteBinding(Batch::class, $id)->toBeNull();

//     $id = $this->group->getRouteKey();

//     expect($this->group)
//         ->resolveChildRouteBinding(Batch::class, $id)->toBeInstanceOf(ProductActions::class);
// });
