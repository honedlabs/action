<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Closure;

use function call_user_func;

trait HasEncoder
{
    /**
     * The encoding closure.
     *
     * @var Closure(string):string|null
     */
    protected static $encoder;

    /**
     * The decoding closure.
     *
     * @var Closure(string):string|null
     */
    protected static $decoder;

    /**
     * Set the encoder.
     *
     * @param  (Closure(string):string)|null  $encoder
     */
    public static function encoder(?Closure $encoder = null): void
    {
        static::$encoder = $encoder;
    }

    /**
     * Set the decoder.
     *
     * @param  (Closure(string):string)|null  $decoder
     */
    public static function decoder(?Closure $decoder = null): void
    {
        static::$decoder = $decoder;
    }

    /**
     * Encode a value using the encoder.
     *
     * @return string
     */
    public static function encode(string $value)
    {
        return isset(static::$encoder)
            ? call_user_func(static::$encoder, $value)
            : base64_encode($value);
    }

    /**
     * Decode a value using the decoder.
     *
     * @return string
     */
    public static function decode(string $value)
    {
        // @phpstan-ignore-next-line
        return isset(static::$decoder)
            ? call_user_func(static::$decoder, $value)
            : base64_decode($value);
    }

    /**
     * Get the id of the instance.
     *
     * @return string
     */
    public function getId()
    {
        return static::encode(static::class);
    }
}
