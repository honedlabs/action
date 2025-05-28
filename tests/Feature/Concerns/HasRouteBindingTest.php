<?php

declare(strict_types=1);

use Honed\Action\ActionGroup;
use Honed\Action\Tests\Stubs\ProductActions;

// beforeEach(function () {
//     $this->test = ActionGroup::make('test');
//     $this->group = ProductActions::make();
// });

// it('has primitive', function () {
//     expect($this->test)
//         ->primitive()->toBe(ActionGroup::class);

//     expect($this->group)
//         ->primitive()->toBe(ActionGroup::class);
// });

// it('has route key', function () {
//     $key = $this->test->getRouteKey();

//     expect($this->test)
//         ->decode($key)->toBe(ActionGroup::class);

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

//     expect(product())
//         ->resolveRouteBinding($id)->toBeNull();
// });

// it('resolves route child binding', function () {
//     $id = $this->test->getRouteKey();

//     // Not a subclass
//     expect($this->test)
//         ->resolveChildRouteBinding(ActionGroup::class, $id)->toBeNull();

//     $id = $this->group->getRouteKey();

//     expect($this->group)
//         ->resolveChildRouteBinding(ActionGroup::class, $id)->toBeInstanceOf(ProductActions::class);
// });
