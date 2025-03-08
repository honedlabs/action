<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Concerns\HasDescription;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Contracts\Resolves;
use Honed\Core\Primitive;

/**
 * @extends Primitive<string,mixed>
 */
class Confirm extends Primitive implements Resolves
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
     * @param  string  $dismiss
     * @param  string  $submit
     * @param  string|null  $intent
     * @return static
     */
    public static function make(
        $label = null,
        $description = null,
        $dismiss = null,
        $submit = null,
        $intent = null
    ) {
        return resolve(static::class)
            ->label($label)
            ->description($description)
            ->dismiss($dismiss)
            ->submit($submit)
            ->intent($intent);
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
        return ! \is_null($this->intent);
    }

    /**
     * Get the submit message for the confirm.
     *
     * @return string
     */
    public function getSubmit()
    {
        if (isset($this->submit)) {
            return $this->submit;
        }

        return $this->fallbackSubmitMessage();
    }

    /**
     * Get the dismiss message for the confirm.
     *
     * @return string
     */
    public function getDismiss()
    {
        if (isset($this->dismiss)) {
            return $this->dismiss;
        }

        return $this->fallbackDismissMessage();
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
    public function resolve($parameters = [], $typed = [])
    {
        $this->resolveLabel($parameters, $typed);
        $this->resolveDescription($parameters, $typed);

        return $this;
    }

    /**
     * Get the submit message for the confirm.
     *
     * @return string
     */
    protected function fallbackSubmitMessage()
    {
        return type(config('action.confirm.submit', 'Confirm'))->asString();
    }

    /**
     * Get the dismiss message for the confirm.
     *
     * @return string
     */
    protected function fallbackDismissMessage()
    {
        return type(config('action.confirm.dismiss', 'Cancel'))->asString();
    }
}
