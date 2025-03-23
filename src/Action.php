<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HasAction;
use Honed\Action\Concerns\HasConfirm;
use Honed\Core\Concerns\Allowable;
use Honed\Core\Concerns\HasExtra;
use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasRoute;
use Honed\Core\Concerns\HasType;
use Honed\Core\Contracts\ResolvesArrayable;
use Honed\Core\Primitive;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Traits\ForwardsCalls;

abstract class Action extends Primitive implements ResolvesArrayable
{
    use Allowable;
    use ForwardsCalls;
    use HasAction;
    use HasConfirm;
    use HasExtra;
    use HasIcon;
    use HasLabel;
    use HasName;
    use HasRoute;
    use HasType;

    /**
     * Create a new action instance.
     *
     * @param  string  $name
     * @param  string|\Closure(mixed...):string|null  $label
     * @return static
     */
    public static function make($name, $label = null)
    {
        return resolve(static::class)
            ->name($name)
            ->label($label ?? static::makeLabel($name));
    }

    /**
     * Execute the action on a resource.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $record
     * @return mixed
     */
    abstract public function execute($record);

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'icon' => $this->getIcon(),
            'extra' => $this->getExtra(),
            'action' => $this->hasAction(),
            'confirm' => $this->getConfirm()?->toArray(),
            'route' => $this->routeToArray(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveToArray($parameters = [], $typed = [])
    {
        return [
            'name' => $this->getName(),
            'label' => $this->resolveLabel($parameters, $typed),
            'type' => $this->getType(),
            'icon' => $this->resolveIcon($parameters, $typed),
            'extra' => $this->resolveExtra($parameters, $typed),
            'action' => $this->hasAction(),
            'confirm' => $this->getConfirm()?->resolveToArray($parameters, $typed),
            'route' => $this->routeToArray($parameters, $typed),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveDefaultClosureDependencyForEvaluationByName($parameterName)
    {
        return match ($parameterName) {
            'confirm' => [$this->confirmInstance()],
            default => [],
        };
    }

    /**
     * {@inheritdoc}
     */
    public function resolveDefaultClosureDependencyForEvaluationByType($parameterType)
    {
        return match ($parameterType) {
            Confirm::class => [$this->confirmInstance()],
            default => [App::make($parameterType)],
        };
    }
}
