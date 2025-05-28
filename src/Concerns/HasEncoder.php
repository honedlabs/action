<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Closure;
use Throwable;

use function call_user_func;
use function class_exists;
use function is_subclass_of;

/**
 * @phpstan-require-implements \Honed\Action\Contracts\Handles
 * @phpstan-require-implements \Illuminate\Contracts\Routing\UrlRoutable
 */
trait HasEncoder
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
     * The root parent class.
     *
     * @return class-string
     */
    abstract public static function baseClass();

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
     * Decode and retrieve a primitive class.
     *
     * @param  string  $value
     * @return mixed
     */
    public static function tryFrom($value)
    {
        try {
            $primitive = static::decode($value);

            // @phpstan-ignore-next-line
            if (class_exists($primitive) && is_subclass_of($primitive, static::baseClass())) {
                return $primitive::make();
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
        return static::tryFrom($value);
    }
}
