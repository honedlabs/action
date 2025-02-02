<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Concerns\HasDescription;
use Honed\Core\Concerns\HasTitle;
use Honed\Core\Contracts\ResolvesClosures;
use Honed\Core\Primitive;

/**
 * @extends Primitive<string,mixed>
 */
class Confirm extends Primitive implements ResolvesClosures
{
    use HasDescription;
    use HasTitle;

    const Constructive = 'constructive';

    const Destructive = 'destructive';

    const Informative = 'informative';

    /**
     * @var string
     */
    protected $dismiss = 'Cancel';

    /**
     * @var string
     */
    protected $submit = 'Confirm';

    /**
     * @var string|null
     */
    protected $intent;

    public function __construct(
        string|\Closure|null $title = null,
        string|\Closure|null $description = null,
        string $dismiss = 'Cancel',
        string $submit = 'Confirm',
        ?string $intent = null
    ) {
        parent::__construct();
        $this->title($title);
        $this->description($description);
        $this->dismiss($dismiss);
        $this->submit($submit);
        $this->intent($intent);
    }

    /**
     * Make a new confirm instance.
     */
    public static function make(
        string|\Closure|null $title = null,
        string|\Closure|null $description = null,
        string $dismiss = 'Cancel',
        string $submit = 'Confirm',
        ?string $intent = null
    ): static {
        return resolve(static::class, \compact('title', 'description', 'dismiss', 'submit', 'intent'));
    }

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
     * @param  array<string,mixed>|\Illuminate\Database\Eloquent\Model  $parameters
     * @param  array<string,mixed>  $typed
     */
    public function resolve($parameters = [], $typed = []): static
    {
        $this->getTitle($parameters, $typed);
        $this->getDescription($parameters, $typed);

        return $this;
    }

    /**
     * Set the dismiss text for the confirm.
     *
     * @return $this
     */
    public function dismiss(?string $dismiss): static
    {
        if (! \is_null($dismiss)) {
            $this->dismiss = $dismiss;
        }

        return $this;
    }

    /**
     * Get the dismiss text for the confirm.
     */
    public function getDismiss(): string
    {
        return $this->dismiss;
    }

    /**
     * Set the submit text for the confirm.
     *
     * @return $this
     */
    public function submit(?string $submit): static
    {
        if (! \is_null($submit)) {
            $this->submit = $submit;
        }

        return $this;
    }

    /**
     * Get the submit text for the confirm.
     */
    public function getSubmit(): string
    {
        return $this->submit;
    }

    /**
     * Set the intent for the confirm.
     *
     * @return $this
     */
    public function intent(?string $intent): static
    {
        if (! \is_null($intent)) {
            $this->intent = $intent;
        }

        return $this;
    }

    /**
     * Get the intent for the confirm.
     */
    public function getIntent(): ?string
    {
        return $this->intent;
    }

    /**
     * Determine if the confirm has an intent set.
     */
    public function hasIntent(): bool
    {
        return ! \is_null($this->intent);
    }

    /**
     * Set the intent as constructive.
     *
     * @return $this
     */
    public function constructive(): static
    {
        return $this->intent(self::Constructive);
    }

    /**
     * Set the intent as destructive.
     *
     * @return $this
     */
    public function destructive(): static
    {
        return $this->intent(self::Destructive);
    }

    /**
     * Set the intent as informative.
     *
     * @return $this
     */
    public function informative(): static
    {
        return $this->intent(self::Informative);
    }
}
