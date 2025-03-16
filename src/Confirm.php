<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Concerns\HasDescription;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Contracts\ResolvesArrayable;
use Honed\Core\Primitive;

/**
 * @extends Primitive<string,mixed>
 */
class Confirm extends Primitive implements ResolvesArrayable
{
    use HasDescription;
    use HasLabel;

    const Constructive = 'constructive';

    const Destructive = 'destructive';

    const Informative = 'informative';

    /**
     * The message to display on the submit button.
     *
     * @var string|null
     */
    protected $submit;

    /**
     * The message to display on the dismiss button.
     *
     * @var string|null
     */
    protected $dismiss;

    /**
     * The intent of the confirm.
     *
     * @var string|null
     */
    protected $intent;

    /**
     * Create a new confirm instance.
     *
     * @param  string|\Closure|null  $label
     * @param  string|\Closure|null  $description
     * @return static
     */
    public static function make(
        $label = null,
        $description = null
    ) {
        return resolve(static::class)
            ->label($label)
            ->description($description);
    }

    /**
     * Set the submit message for the confirm.
     *
     * @param  string|null  $submit
     * @return $this
     */
    public function submit($submit)
    {
        $this->submit = $submit;

        return $this;
    }

    /**
     * Set the dismiss message for the confirm.
     *
     * @param  string|null  $dismiss
     * @return $this
     */
    public function dismiss($dismiss)
    {
        $this->dismiss = $dismiss;

        return $this;
    }

    /**
     * Set the intent of the confirm.
     *
     * @param  string|null  $intent
     * @return $this
     */
    public function intent($intent)
    {
        $this->intent = $intent;

        return $this;
    }

    /**
     * Set the intent as constructive.
     *
     * @return $this
     */
    public function constructive()
    {
        return $this->intent(self::Constructive);
    }

    /**
     * Set the intent as destructive.
     *
     * @return $this
     */
    public function destructive()
    {
        return $this->intent(self::Destructive);
    }

    /**
     * Set the intent as informative.
     *
     * @return $this
     */
    public function informative()
    {
        return $this->intent(self::Informative);
    }

    /**
     * Determine if the confirm has an intent set.
     *
     * @return bool
     */
    public function hasIntent()
    {
        return isset($this->intent);
    }

    /**
     * Get the submit message for the confirm.
     *
     * @return string
     */
    public function getSubmit()
    {
        return $this->submit ?? static::fallbackSubmitMessage();
    }

    /**
     * Get the submit message for the confirm from the config.
     *
     * @return string
     */
    public static function fallbackSubmitMessage()
    {
        return type(config('action.submit', 'Confirm'))->asString();
    }

    /**
     * Get the dismiss message for the confirm.
     *
     * @return string
     */
    public function getDismiss()
    {
        return $this->dismiss ?? static::fallbackDismissMessage();
    }

    /**
     * Get the dismiss message for the confirm from the config.
     *
     * @return string
     */
    public static function fallbackDismissMessage()
    {
        return type(config('action.dismiss', 'Cancel'))->asString();
    }

    /**
     * Get the intent of the confirm.
     *
     * @return string|null
     */
    public function getIntent()
    {
        return $this->intent;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'label' => $this->getLabel(),
            'description' => $this->getDescription(),
            'dismiss' => $this->getDismiss(),
            'submit' => $this->getSubmit(),
            'intent' => $this->getIntent(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveToArray($parameters = [], $typed = [])
    {
        return [
            'label' => $this->resolveLabel($parameters, $typed),
            'description' => $this->resolveDescription($parameters, $typed),
            'dismiss' => $this->getDismiss(),
            'submit' => $this->getSubmit(),
            'intent' => $this->getIntent(),
        ];
    }
}
