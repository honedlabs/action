<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Primitive;
use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasRoute;
use Honed\Core\Concerns\Allowable;
use Honed\Core\Contracts\ResolvesClosures;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @extends \Honed\Core\Primitive<string,mixed>
 */
abstract class Action extends Primitive implements ResolvesClosures
{
    use Allowable;
    use HasIcon;
    use HasLabel;
    use HasName;
    use HasType;
    use HasRoute;
    use ForwardsCalls;
    use Concerns\HasAction;

    public function __construct(?string $name = null, string|\Closure|null $label = null)
    {
        parent::__construct();

        $this->name($name);
        $this->label($label ?? $this->makeLabel($name));
    }

    public static function make(?string $name = null, string|\Closure|null $label = null): static
    {
        return resolve(static::class, \compact('name', 'label'));
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            ...($this->hasIcon() ? ['icon' => $this->getIcon()] : []),
            ...($this->hasRoute() 
                ? [
                    'href' => $this->getRoute(),
                    'method' => $this->getMethod(),
                ] : [])
        ];
    }

    /**
     * @param  array<string,mixed>|\Illuminate\Database\Eloquent\Model  $parameters
     * @param  array<string,mixed>  $typed
     * 
     * @return $this
     */
    public function resolve($parameters = [], $typed = []): static
    {
        $this->getLabel($parameters, $typed);
        $this->getName($parameters, $typed);
        $this->getIcon($parameters, $typed);
        // $this->getExtra($parameters, $typed);

        return $this;
    }
}
