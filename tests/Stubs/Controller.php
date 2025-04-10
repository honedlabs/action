<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Stubs;

use Honed\Action\BulkAction;
use Honed\Action\Handler;
use Honed\Action\Http\Requests\InvokableRequest;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(InvokableRequest $request)
    {
        $builder = Product::query();

        $response = Handler::make(
            $builder,
            $this->getActions(),
        )->handle($request);

        return $response;
    }

    protected function getActions(): array
    {
        return [
            InlineAction::make('update.name')
                ->action(fn ($product) => $product->update(['name' => 'test'])),

            InlineAction::make('show')
                ->route(fn ($product) => route('products.show', $product->public_id)),

            InlineAction::make('price.100')
                ->action(function ($product) {
                    $product->update(['price' => 100]);

                    return inertia('Show');
                }),

            InlineAction::make('update.description')
                ->action(fn ($product) => $product->update(['description' => 'test']))
                ->allow(false),

            BulkAction::make('update.name')
                ->action(fn ($product) => $product->update(['name' => 'test']))
                ->allow(false),

            BulkAction::make('update.description')
                ->action(fn ($product) => $product->update(['description' => 'test'])),

            BulkAction::make('price.50')
                ->action(function (Collection $products) {
                    $products->each->update(['price' => 50]);

                    return inertia('Show');
                }),

            PageAction::make('create.product.name')
                ->action(fn () => $this->createProduct('name', 'name')),

            PageAction::make('create.product.description')
                ->action(fn () => $this->createProduct('description', 'description'))
                ->allow(false),

            PageAction::make('create')
                ->route('products.create'),

            PageAction::make('price.10')
                ->action(function (Builder $builder) {
                    $builder->update(['price' => 10]);

                    return inertia('Show');
                }),
        ];
    }

    protected function createProduct(string $name, string $description): Product
    {
        return Product::create([
            'name' => $name,
            'description' => $description,
        ]);
    }
}
