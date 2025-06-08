<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Closure;

/**
 * @phpstan-require-extends \Honed\Action\Contracts\HandlesActions
 */
trait HasHandler
{
    /**
     * The encoding closure.
     *
     * @var Closure(mixed):string|null
     */
    protected static $encoder;

    /**
     * The decoding closure.
     *
     * @var Closure(string):mixed|null
     */
    protected static $decoder;

    /**
     * The endpoint to execute server actions.
     *
     * @var string|null
     */
    protected $endpoint;

    /**
     * Whether the instance can execute server actions.
     *
     * @var bool
     */
    protected $execute = true;

    /**
     * The key to use for selecting records.
     * 
     * @var string|null
     */
    protected $key;

    /**
     * The root parent class, indicating an anonymous class.
     * 
     * @return class-string<\Honed\Action\Contracts\HandlesActions>
     */
    abstract public static function anonymous();

    /**
     * Handle the incoming action request.
     *
     * @param  \Honed\Action\Http\Requests\InvokableRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    abstract public function handle($request);

    /**
     * Set the endpoint to execute server actions.
     *
     * @param  string|null  $endpoint
     * @return $this
     */
    public function endpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Get the endpoint to execute server actions.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint ?? static::defaultEndpoint();
    }

    /**
     * Get the default endpoint to execute server actions.
     *
     * @return string
     */
    protected static function defaultEndpoint()
    {
        /** @var string|null */
        return config('action.endpoint', 'actions');
    }

    /**
     * Set the encoder.
     *
     * @param  (Closure(mixed):string)|null  $encoder
     * @return void
     */
    public static function encoder($encoder = null)
    {
        static::$encoder = $encoder;
    }

    /**
     * Set the decoder.
     *
     * @param  (Closure(string):mixed)|null  $decoder
     * @return void
     */
    public static function decoder($decoder = null)
    {
        static::$decoder = $decoder;
    }

    /**
     * Encode a value using the encoder.
     *
     * @param  mixed  $value
     * @return string
     */
    public static function encode($value)
    {
        return isset(static::$encoder)
            ? call_user_func(static::$encoder, $value)
            : encrypt($value);
    }

    /**
     * Decode a value using the decoder.
     *
     * @param  string  $value
     * @return mixed
     */
    public static function decode($value)
    {
        // @phpstan-ignore-next-line
        return isset(static::$decoder)
            ? call_user_func(static::$decoder, $value)
            : decrypt($value);
    }

    /**
     * Find a primitive class from the encoded value.
     * 
     * @param  string  $value
     * @return mixed
     */
    public static function find($value)
    {
        try {
            $primitive = static::decode($value);

            // @phpstan-ignore-next-line
            if (class_exists($primitive) 
                && is_subclass_of($primitive, static::anonymous())
            ) {
                return $primitive::make();
            }

            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

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
     * Set the key to use for selecting records.
     *
     * @param  string|null  $key
     * @return $this
     */
    public function key($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the key to use for selecting records.
     *
     * @return string|null
     */
    public function getKey()
    {
        return $this->key;
    }
}
