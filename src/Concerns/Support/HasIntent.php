<?php

declare(strict_types=1);

namespace Honed\Action\Concerns\Support;

trait HasIntent
{
    const Constructive = 'constructive';

    const Destructive = 'destructive';

    const Informative = 'informative';

    /**
     * The intent of the confirm.
     *
     * @var string|null
     */
    protected $intent;

    /**
     * Set the intent of the confirm.
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
     * Get the intent of the confirm.
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
