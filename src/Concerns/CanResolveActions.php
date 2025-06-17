<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Illuminate\Support\Facades\App;

trait CanResolveActions
{
    use HasActions;
    use HasEndpoint;
    use CanBeExecutable;
    use HasEncoder;

        /**
     * Decode and retrieve a primitive class.
     *
     * @param  string  $value
     * @return mixed
     */
    public static function find($value)
    {
        try {
            $primitive = static::decode($value);

            // @phpstan-ignore-next-line
            if (class_exists($primitive)) {
                return $primitive::make();
            }

            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    /**
     * Get the handler for the instance.
     *
     * @return class-string<Handler>
     */
    abstract public function getHandler();

    /**
     * Retrieve the child model for a bound value.
     *
     * @param  string  $childType
     * @param  string  $value
     * @param  string|null  $field
     * @return static|null
     */
    public function resolveChildRouteBinding($childType, $value, $field = null)
    {
        return $this->resolveRouteBinding($value, $field);
    }

    /**
     * Get the value of the model's route key.
     *
     * @return string
     */
    public function getRouteKey()
    {
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
     * @param  \Honed\Action\Http\Requests\InvokableRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handle($request)
    {
        if ($this->isNotExecutable()) {
            abort(404);
        }

        return App::make($this->getHandler())
            ->handle($this, $request);
    }
}