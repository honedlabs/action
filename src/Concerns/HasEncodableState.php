<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

use function call_user_func;

trait HasEncodableState
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

    // /**
    //  * The data to remember when serializing.
    //  *
    //  * @var array<string, mixed>
    //  */
    // protected $remember = [];

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

    // /**
    //  * Set data to remember when serializing.
    //  *
    //  * @param  \Illuminate\Database\Eloquent\Model|array<string, mixed>  $data
    //  * @return $this
    //  */
    // public function remember($data)
    // {
    //     $this->remember = match (true) {
    //         $data instanceof Model => [$data::class => $data->getRouteKey()],
    //         default => $data,
    //     };

    //     return $this;
    // }

    // /**
    //  * Get the data that is remembered when serializing.
    //  *
    //  * @return array<string, mixed>
    //  */
    // public function getRemembered()
    // {
    //     return $this->remember;
    // }

    // /**
    //  * Get the
    //  */
    // public function getEncodedState()
    // {
    //     return static::encode([
    //         'id' => static::class,
    //         ...$this->remember
    //     ]);
    // }
}
