<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

use Illuminate\Contracts\Routing\UrlRoutable;

/**
 * @phpstan-require-extends \Honed\Core\Primitive
 */
interface HandlesOperations extends UrlRoutable
{
    /**
     * Find a primitive class from the encoded value.
     *
     * @param  string  $value
     * @return static|null
     */
    public static function find($value);

    /**
     * Get the parent class for the instance.
     *
     * @return class-string<HandlesOperations>
     */
    public static function getParentClass();

    /**
     * Get the handler for the instance.
     *
     * @return class-string<\Honed\Action\Handlers\Handler<self>>
     */
    public function getHandler(); // @phpstan-ignore-line

    /**
     * Handle the incoming action request.
     *
     * @param  \Honed\Action\Http\Requests\InvokableRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handle($request);
}
