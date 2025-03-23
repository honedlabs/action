<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Contracts\ResolvesArrayable;
use Honed\Core\Primitive;

class Confirm extends Primitive implements ResolvesArrayable
{
    const Constructive = 'constructive';

    const Destructive = 'destructive';

    const Informative = 'informative';

    /**
     * The title of the confirm.
     *
     * @var string|\Closure(mixed...):string|null
     */
    protected $title;

    /**
     * The description of the confirm
     *
     * @var string|\Closure(mixed...):string|null
     */
    protected $description;

    /**
     * The intent of the confirm.
     *
     * @var string|null
     */
    protected $intent;

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
     * Create a new confirm instance.
     *
     * @param  string|\Closure(mixed...):string|null  $title
     * @param  string|\Closure(mixed...):string|null  $description
     * @return static
     */
    public static function make($title = null, $description = null)
    {
        return resolve(static::class)
            ->title($title)
            ->description($description);
    }

    /**
     * Set the title of the confirm.
     *
     * @param  string|\Closure(mixed...):string|null  $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the title of the confirm.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->evaluate($this->title);
    }

    /**
     * Resolve the title of the confirm.
     *
     * @param  array<string, mixed>  $parameters
     * @param  array<class-string, mixed>  $typed
     * @return string|null
     */
    public function resolveTitle($parameters = [], $typed = [])
    {
        return $this->evaluate($this->title, $parameters, $typed);
    }

    /**
     * Set the description of the confirm.
     *
     * @param  string|\Closure(mixed...):string|null  $description
     * @return $this
     */
    public function description($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description of the confirm.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->evaluate($this->description);
    }

    /**
     * Resolve the description of the confirm.
     *
     * @param  array<string, mixed>  $parameters
     * @param  array<class-string, mixed>  $typed
     * @return string|null
     */
    public function resolveDescription($parameters = [], $typed = [])
    {
        return $this->evaluate($this->description, $parameters, $typed);
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
     * Get the intent of the confirm.
     *
     * @return string|null
     */
    public function getIntent()
    {
        return $this->intent;
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
     * Get the submit message for the confirm.
     *
     * @return string
     */
    public function getSubmit()
    {
        return $this->submit ?? static::getDefaultSubmit();
    }

    /**
     * Get the submit message for the confirm from the config.
     *
     * @return string
     */
    public static function getDefaultSubmit()
    {
        return type(config('action.submit', 'Confirm'))->asString();
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
     * Get the dismiss message for the confirm.
     *
     * @return string
     */
    public function getDismiss()
    {
        return $this->dismiss ?? static::getDefaultDismiss();
    }

    /**
     * Get the dismiss message for the confirm from the config.
     *
     * @return string
     */
    public static function getDefaultDismiss()
    {
        return type(config('action.dismiss', 'Cancel'))->asString();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'title' => $this->getTitle(),
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
            'title' => $this->resolveTitle($parameters, $typed),
            'description' => $this->resolveDescription($parameters, $typed),
            'dismiss' => $this->getDismiss(),
            'submit' => $this->getSubmit(),
            'intent' => $this->getIntent(),
        ];
    }
}
