<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Concerns\HasDescription;
use Honed\Core\Concerns\HasName;
use Honed\Core\Contracts\Resolves;
use Honed\Core\Primitive;

/**
 * @extends Primitive<string,mixed>
 */
class Confirm extends Primitive implements Resolves
{
    use Concerns\Support\HasDismissMessage;
    use Concerns\Support\HasIntent;
    use Concerns\Support\HasSubmitMessage;
    use HasDescription;
    use HasName;

    /**
     * Create a new confirm instance.
     */
    public static function make(
        string|\Closure|null $name = null,
        string|\Closure|null $description = null,
        string $dismiss = 'Cancel',
        string $submit = 'Confirm',
        ?string $intent = null
    ): static {
        return resolve(static::class)
            ->name($name)
            ->description($description)
            ->dismiss($dismiss)
            ->submit($submit)
            ->intent($intent);
    }

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
     */
    public function resolve(array $parameters = [], array $typed = []): static
    {
        $this->resolveName($parameters, $typed);
        $this->resolveDescription($parameters, $typed);

        return $this;
    }
}
