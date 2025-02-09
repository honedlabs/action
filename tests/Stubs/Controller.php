<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Stubs;

use Honed\Action\Handler;
use Honed\Action\BulkAction;
use Honed\Action\PageAction;
use Honed\Action\InlineAction;
use Honed\Action\Http\Requests\ActionRequest;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(ActionRequest $request)
    {
        $builder = Product::query();

        $response = Handler::make($builder, $this->getActions())
            ->handle($request);

        return $response;
    }

    protected function getActions(): array
    {
        return [
            InlineAction::make('update.name')
                ->action(fn ($product) => $product->update(['name' => 'test'])),

            InlineAction::make('update.description')
                ->action(fn ($product) => $product->update(['description' => 'test']))
                ->allow(false),

            BulkAction::make('update.name')
                ->action(fn ($product) => $product->update(['name' => 'test'])),

            BulkAction::make('update.description')
                ->action(fn ($product) => $product->update(['description' => 'test']))
                ->allow(false),

            PageAction::make('create.product.name')
                ->action(fn () => $this->createProduct('name', 'name')),

            PageAction::make('create.product.description')
                ->action(fn () => $this->createProduct('description', 'description'))
                ->allow(false),
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
