<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Operations\Operation;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @phpstan-require-implements \Honed\Action\Contracts\HandlesOperations
 */
trait CanHandleOperations
{
    use Actionable;
    use HasEncoder;
    use HasOperations;

    /**
     * Decode and retrieve a primitive class.
     */
    public static function find(string $value): ?static
    {
        try {
            $primitive = static::decode($value);

            if (static::canHandleOperations($primitive)) {
                return $primitive::make(); // @phpstan-ignore-line
            }

            return null;

        } catch (Throwable $th) {
            return null;
        }
    }

    /**
     * Retrieve the child model for a bound value.
     *
     * @param  string  $childType
     * @param  string  $value
     * @param  string|null  $field
     * @return mixed
     */
    public function resolveChildRouteBinding($childType, $value, $field = null)
    {
        if ($childType === 'operation') {
            return Arr::first(
                $this->getOperations(),
                static fn ($operation) => $operation->getName() === $value
            );
        }

        return $this->resolveRouteBinding($value, $field);
    }

    /**
     * Get the value of the model's route key.
     *
     * @return string
     */
    public function getRouteKey()
    {
        // return $this->getId();
        return static::encode(static::class);
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  string  $value
     * @param  string|null  $field
     * @return static|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        /** @var static|null */
        return static::find($value);
    }

    /**
     * Handle the incoming action request.
     *
     * @return Responsable|Response
     */
    public function handle(Operation $operation, Request $request): mixed
    {
        $handler = $this->getHandler();

        return $handler::make($this)->handle($operation, $request);
    }

    /**
     * Get the actionable configuration as an array.
     *
     * @return array<string, mixed>
     */
    public function actionableToArray(): array
    {
        if ($this->isActionable()) {
            return [
                'id' => $this->getRouteKey(),
                'endpoint' => $this->getEndpoint(),
            ];
        }

        return [];
    }

    /**
     * Determine if the primitive can handle operations.
     */
    protected static function canHandleOperations(mixed $primitive): bool
    {
        return is_string($primitive)
            && class_exists($primitive)
            && is_subclass_of($primitive, static::getParentClass()); // @phpstan-ignore function.alreadyNarrowedType
    }
}
