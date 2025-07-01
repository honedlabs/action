<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

use Honed\Action\Operations\Operation;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface HandlesOperations extends UrlRoutable
{
    /**
     * Find a primitive class from the encoded value.
     */
    public static function find(string $value): ?static;

    /**
     * Get the parent class for the instance.
     *
     * @return class-string<self>
     */
    public static function getParentClass(): string;

    /**
     * Get the handler for the instance.
     *
     * @return class-string
     */
    public function getHandler(): string;

    /**
     * Handle the incoming action request.
     *
     * @return Responsable|Response
     */
    public function handle(Operation $operation, Request $request): mixed;
}
