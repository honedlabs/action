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
     *
     * @param  string|\Closure|null  $name
     * @param  string|\Closure|null  $description
     * @param  string  $dismiss
     * @param  string  $submit
     * @param  string|null  $intent
     * @return static
     */
    public static function make(
        $name = null,
        $description = null,
        $dismiss = 'Cancel',
        $submit = 'Confirm',
        $intent = null
    ) {
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
    public function toArray()
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
    public function resolve($parameters = [], $typed = [])
    {
        $this->resolveName($parameters, $typed);
        $this->resolveDescription($parameters, $typed);

        return $this;
    }
}
