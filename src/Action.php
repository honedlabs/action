<?php

declare(strict_types=1);

namespace Honed\Action;

use Closure;
use Honed\Action\Concerns\HasAction;
use Honed\Action\Concerns\HasConfirm;
use Honed\Core\Concerns\Allowable;
use Honed\Core\Concerns\HasExtra;
use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasRoute;
use Honed\Core\Concerns\HasType;
use Honed\Core\Primitive;
use Illuminate\Support\Facades\App;

use function is_string;
use function str_ends_with;
use function str_starts_with;

abstract class Action extends Primitive
{
    use Allowable;
    use HasAction;
    use HasConfirm;
    use HasExtra;
    use HasIcon;
    use HasLabel;
    use HasName;
    use HasRoute;
    use HasType;

    /**
     * Execute the action on a resource.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $record
     * @return mixed
     */
    abstract public function execute($record);

    /**
     * Create a new action instance.
     *
     * @param  string  $name
     * @param  string|Closure(mixed...):string|null  $label
     * @return static
     */
    public static function make($name, $label = null)
    {
        return resolve(static::class)
            ->name($name)
            ->label($label ?? static::makeLabel($name));
    }

    /**
     * Set the route.
     *
     * @param  string|Closure(mixed...):string  $route
     * @param  mixed  $parameters
     * @return $this
     */
    public function route($route, $parameters = [])
    {
        $this->route = match (true) {
            $route instanceof Closure => $route,
            $this->isBindingParameter($parameters) => fn ($model) => route($route, $model, true),
            default => route($route, $parameters, true),
        };

        return $this;
    }

    /**
     * Determine if the action is an inline action.
     *
     * @return bool
     */
    public function isInline()
    {
        return $this instanceof InlineAction;
    }

    /**
     * Determine if the action is a bulk action.
     *
     * @return bool
     */
    public function isBulk()
    {
        return $this instanceof BulkAction;
    }

    /**
     * Determine if the action is a page action.
     *
     * @return bool
     */
    public function isPage()
    {
        return $this instanceof PageAction;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($named = [], $typed = [])
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel($named, $typed),
            'type' => $this->getType(),
            'icon' => $this->getIcon($named, $typed),
            'extra' => $this->getExtra($named, $typed),
            'actionable' => $this->isActionable(),
            'confirm' => $this->getConfirm()?->toArray($named, $typed),
            'route' => $this->routeToArray($named, $typed),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveDefaultClosureDependencyForEvaluationByName($parameterName)
    {
        if (isset($this->parameters[$parameterName])) {
            return [$this->parameters[$parameterName]];
        }

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

    /**
     * Determine if the parameters are implicit route-model bindings to be evaluated.
     *
     * @param  mixed  $parameters
     * @return bool
     */
    protected function isBindingParameter($parameters)
    {
        return is_string($parameters)
            && str_starts_with($parameters, '{')
            && str_ends_with($parameters, '}');
    }
}
