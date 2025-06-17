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

    public const INLINE = 'inline';
    public const BULK = 'bulk';
    public const PAGE = 'page';

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
     * Define the action instance.
     *
     * @param  $this  $action
     * @return $this
     */
    public function definition(self $action): self
    {
        return $action;
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
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->definition($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName($parameterName)
    {
        if (isset($this->parameters[$parameterName])) {
            return [$this->parameters[$parameterName]];
        }

        return match ($parameterName) {
            'confirm' => [$this->confirmInstance()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType($parameterType)
    {
        return match ($parameterType) {
            Confirm::class => [$this->confirmInstance()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
