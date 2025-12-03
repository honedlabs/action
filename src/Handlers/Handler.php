<?php

declare(strict_types=1);

namespace Honed\Action\Handlers;

use Honed\Action\Handlers\Concerns\Parameterisable;
use Honed\Action\Handlers\Concerns\Preparable;
use Honed\Action\Operations\Operation;
use Honed\Action\Unit;
use Honed\Core\Concerns\HasInstance;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * @template TClass of \Honed\Action\Unit = \Honed\Action\Unit
 *
 * @mixin TClass
 */
class Handler
{
    /**
     * @use HasInstance<TClass>
     */
    use HasInstance;

    use Parameterisable;
    use Preparable;

    /**
     * Create a new instance of the handler.
     *
     * @param  TClass  $instance
     */
    public static function make(Unit $instance): static
    {
        return app(static::class)->instance($instance);
    }

    /**
     * Handle the incoming request using the operations from the source, and the resource provided.
     *
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     * @throws TooManyRequestsHttpException
     */
    public function handle(Operation $operation, Request $request): Responsable|Response
    {
        if (! $request->isMethod($operation->getMethod())) {
            throw new MethodNotAllowedHttpException(
                [$operation->getMethod()],
                "The {$request->getMethod()} method is not supported for this action."
            );
        }

        $this->prepare($operation, $request);

        if ($this->isForbidden($operation)) {
            throw new AccessDeniedHttpException(
                'You are not allowed to perform this action.'
            );
        }

        $attempts = $operation->getRateLimit();

        $response = match (true) {
            $attempts !== null => $this->callRateLimited($operation, $attempts),
            default => $this->call($operation),
        };

        return $this->respond($operation, $response);
    }

    /**
     * Get the resource for the handler.
     *
     * @return array<array-key, mixed>|Builder<Model>
     */
    protected function getResource(): array|Builder
    {
        return $this->instance->getBuilder();
    }

    /**
     * Get the key to use for selecting records.
     */
    protected function getKey(): string
    {
        return $this->instance->getKey()
            ?? $this->getBuilder()->getModel()->getKeyName();
    }

    /**
     * Determine if the operation is forbidden.
     */
    protected function isForbidden(Operation $operation): bool
    {
        return ! $operation->isAllowed($this->named, $this->typed);
    }

    /**
     * Call the operation callback with a rate limit in place.
     *
     *
     * @throws TooManyRequestsHttpException
     */
    protected function callRateLimited(Operation $operation, int $attempts): mixed
    {
        $key = $operation->getRateLimitBy($this->named, $this->typed);

        if (RateLimiter::tooManyAttempts($key, $attempts)) {
            throw new TooManyRequestsHttpException(
                message: 'You have made too many requests for this action. Please try again later.'
            );
        }

        RateLimiter::increment($key);

        return $this->call($operation);
    }

    /**
     * Call the operation callback.
     */
    protected function call(Operation $operation): mixed
    {
        if ($operation->hasAction()) {
            return $this->evaluate($operation->getHandler(), $this->named, $this->typed);
        }

        $url = $operation->getUrl($this->named, $this->typed);

        return $url ? redirect($url) : back();
    }

    /**
     * Prepare the data and instance to handle the operation.
     */
    protected function prepare(Operation $operation, Request $request): void
    {
        /** @var array<array-key, mixed>|Model|Builder<Model> */
        $resource = match (true) {
            $operation->isInline() => $this->prepareForInlineOperation($request),
            $operation->isBulk() => $this->prepareForBulkOperation($request),
            default => $this->prepareForPageOperation($request)
        };

        if ($resource) {
            $this->parameterise($resource);
        }
    }

    /**
     * Respond to the operation.
     */
    protected function respond(
        Operation $operation,
        mixed $response
    ): Response|Responsable {

        return match (true) {
            $operation->hasRedirect() => $operation->callRedirect($this->named, $this->typed),
            $response instanceof Responsable,
            $response instanceof Response => $response,
            default => back()
        };
    }

    /**
     * Get the record for the given id.
     *
     * @return array<string, mixed>|Model|null
     */
    protected function getRecord(int|string $id): array|Model|null
    {
        /** @var array<string, mixed>|Model|null */
        return $this->instance->evaluate(
            // @phpstan-ignore-next-line
            fn (Builder $builder) => $builder->firstWhere($this->getKey(), $id)
        );
    }

    /**
     * Apply an exception clause to the record builder.
     *
     * @param  array<int, mixed>  $ids
     * @return array<int, mixed>|Builder<Model>
     */
    protected function getException(array $ids): array|Builder
    {
        /** @var array<int, mixed>|Builder<Model> */
        return $this->instance->evaluate(
            // @phpstan-ignore-next-line
            fn (Builder $builder) => $builder->whereNotIn($this->getKey(), $ids)
        );
    }

    /**
     * Apply an inclusion clause to the record builder.
     *
     * @param  array<int, mixed>  $ids
     * @return array<int, mixed>|Builder<Model>
     */
    protected function getOnly(array $ids): array|Builder
    {
        /** @var array<int, mixed>|Builder<Model> */
        return $this->instance->evaluate(
            // @phpstan-ignore-next-line
            fn (Builder $builder) => $builder->whereIn($this->getKey(), $ids)
        );
    }
}
