<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Concerns\HasDescription;
use Honed\Core\Concerns\HasName;
use Honed\Core\Primitive;

/**
 * @extends Primitive<string,mixed>
 */
class Confirm extends Primitive
{
    use HasDescription;
    use HasName;

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

    /**
     * Make a new confirm instance.
     */
    public static function make(string|\Closure|null $name = null, string|\Closure|null $description = null, string $dismiss = 'Cancel', string $submit = 'Confirm', ?string $intent = null): static
    {
        return resolve(static::class)
            ->name($name)
            ->description($description)
            ->dismiss($dismiss)
            ->submit($submit)
            ->intent($intent);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'dismiss' => $this->getDismiss(),
            'submit' => $this->getSubmit(),
            'intent' => $this->getIntent(),
        ];
    }

    /**
     * Resolve the confirm's properties.
     *
     * @param  array<string,mixed>  $parameters
     * @param  array<string,mixed>  $typed
     * @return $this
     */
    public function resolve(array $parameters = [], array $typed = []): static
    {
        $this->resolveName($parameters, $typed);
        $this->resolveDescription($parameters, $typed);

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
