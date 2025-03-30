<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

trait HasEncoder
{
    /**
     * The encoding closure.
     *
     * @var \Closure(mixed):string|null
     */
    protected static $encoder;

    /**
     * The decoding closure.
     *
     * @var \Closure(string):mixed|null
     */
    protected static $decoder;

    /**
     * Set the encoder.
     *
     * @param  (\Closure(mixed):string)|null  $encoder
     * @return void
     */
    public static function encoder($encoder = null)
    {
        static::$encoder = $encoder;
    }

    /**
     * Set the decoder.
     *
     * @param  (\Closure(string):mixed)|null  $decoder
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
            ? \call_user_func(static::$encoder, $value)
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
            ? \call_user_func(static::$decoder, $value)
            : decrypt($value);
    }

    /**
     * Decode and retrieve a primitive class.
     *
     * @param  string  $value
     * @param  class-string  $class
     * @return mixed
     */
    public static function getPrimitive($value, $class)
    {
        try {
            $primitive = static::decode($value);

            // @phpstan-ignore-next-line
            if (\class_exists($primitive) && \is_subclass_of($primitive, $class)) {
                return $primitive::make();
            }

            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }
}
