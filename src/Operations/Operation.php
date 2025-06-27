<?php

declare(strict_types=1);

namespace Honed\Action\Operations;

use Closure;
use Honed\Action\Confirm;
use Honed\Action\Operations\Concerns\CanBeRateLimited;
use Honed\Action\Operations\Concerns\CanRedirect;
use Honed\Action\Operations\Concerns\Confirmable;
use Honed\Action\Operations\Concerns\HasAction;
use Honed\Action\Operations\Concerns\IsInertia;
use Honed\Core\Concerns\Allowable;
use Honed\Core\Concerns\CanHaveIcon;
use Honed\Core\Concerns\CanHaveUrl;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasName;
use Honed\Core\Contracts\NullsAsUndefined;
use Honed\Core\Primitive;

abstract class Operation extends Primitive implements NullsAsUndefined
{
    use Allowable;
    use CanBeRateLimited;
    use CanHaveIcon;
    use CanHaveUrl;
    use CanRedirect;
    use Confirmable;
    use HasAction;
    use HasLabel;
    use HasName;
    use IsInertia;

    public const INLINE = 'inline';

    public const BULK = 'bulk';

    public const PAGE = 'page';

    /**
     * The identifier to use for evaluation.
     *
     * @var string
     */
    protected $evaluationIdentifier = 'operation';

    /**
     * Get the type of the operation.
     */
    abstract protected function type(): string;

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
     * Execute the inline action on the given record.
     *
     * @return Closure|null
     */
    public function callback()
    {
        return $this->getHandler();
    }

    /**
     * Determine if the action is an inline action.
     *
     * @return bool
     */
    public function isInline()
    {
        return $this instanceof InlineOperation;
    }

    /**
     * Determine if the action is a bulk action.
     *
     * @return bool
     */
    public function isBulk()
    {
        return $this instanceof BulkOperation;
    }

    /**
     * Determine if the action is a page action.
     *
     * @return bool
     */
    public function isPage()
    {
        return $this instanceof PageOperation;
    }

    /**
     * Get the representation of the instance.
     *
     * @return array<string, mixed>
     */
    protected function representation(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->type(),
            'icon' => $this->getIcon(),
            'action' => $this->hasAction(),
            'confirm' => $this->getConfirm()?->toArray(),
            'route' => $this->urlToArray(),
            'inertia' => $this->isInertia() ?: null,
        ];
    }

    /**
     * Provide a selection of default dependencies for evaluation by name.
     *
     * @param  string  $parameterName
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName($parameterName)
    {
        if (isset($this->parameters[$parameterName])) {
            return [$this->parameters[$parameterName]];
        }

        return match ($parameterName) {
            'confirm' => [$this->newConfirm()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * Provide a selection of default dependencies for evaluation by type.
     *
     * @param  string  $parameterType
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType($parameterType)
    {
        return match ($parameterType) {
            self::class => [$this],
            Confirm::class => [$this->newConfirm()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
