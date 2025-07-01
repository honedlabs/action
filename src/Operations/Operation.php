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
use Honed\Action\Unit;
use Honed\Core\Concerns\Allowable;
use Honed\Core\Concerns\CanHaveIcon;
use Honed\Core\Concerns\CanHaveTarget;
use Honed\Core\Concerns\CanHaveUrl;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasMethod;
use Honed\Core\Concerns\HasName;
use Honed\Core\Contracts\NullsAsUndefined;
use Honed\Core\Primitive;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Http\Request;

class Operation extends Primitive implements NullsAsUndefined, UrlRoutable
{
    use Allowable;
    use CanBeRateLimited;
    use CanHaveIcon;
    use CanHaveTarget;
    use CanHaveUrl;
    use CanRedirect;
    use Confirmable;
    use HasAction;
    use HasLabel;
    use HasMethod;
    use HasName;
    use IsInertia;

    /**
     * The identifier to use for evaluation.
     *
     * @var string
     */
    protected $evaluationIdentifier = 'operation';

    /**
     * Create a new action instance.
     *
     * @param  string|Closure(mixed...):string|null  $label
     */
    public static function make(string $name, string|Closure|null $label = null): static
    {
        return resolve(static::class)
            ->name($name)
            ->label($label ?? static::makeLabel($name));
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'operation';
    }

    /**
     * Get the value of the model's route key.
     */
    public function getRouteKey(): string
    {
        /** @var string */
        return $this->getName();
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  string  $value
     * @param  string|null  $field
     * @return static|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->getName() === $value ? $this : null;
    }

    /**
     * Retrieve the child model for a bound value.
     *
     * @param  string  $childType
     * @param  string  $value
     * @param  string|null  $field
     * @return static|null
     */
    public function resolveChildRouteBinding($childType, $value, $field = null)
    {
        return $this->resolveRouteBinding($value, $field);
    }

    /**
     * Execute the inline action on the given record.
     */
    public function callback(): ?Closure
    {
        return $this->getHandler();
    }

    /**
     * Determine if the action is an inline action.
     */
    public function isInline(): bool
    {
        return $this instanceof InlineOperation;
    }

    /**
     * Determine if the action is a bulk action.
     */
    public function isBulk(): bool
    {
        return $this instanceof BulkOperation;
    }

    /**
     * Determine if the action is a page action.
     */
    public function isPage(): bool
    {
        return $this instanceof PageOperation;
    }

    /**
     * Determine the type of the operation.
     *
     * @return 'anchor' | 'inertia' | null
     */
    public function getType(): ?string
    {
        if (! $this->hasUrl()) {
            return null;
        }

        return $this->isInertia() ? 'inertia' : 'anchor';
    }

    /**
     * Mount the operation to a unit.
     *
     * @return $this
     */
    public function mount(Unit $unit): static
    {
        if ($this->hasAction() && ! $this->hasUrl()) {
            /** @var string $name */
            $name = config('action.name', 'actions');

            $this->url(route($name, [$unit, $this->getName()], true));
        }

        return $this;
    }

    /**
     * Get the representation of the instance.
     *
     * @return array<string, mixed>
     */
    protected function representation(): array
    {
        $url = $this->hasUrl() ? [
            'method' => $this->getMethod(),
            'href' => $this->getUrl(),
            'target' => $this->getTarget(),
        ] : [];

        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'icon' => $this->getIcon(),
            'confirm' => $this->getConfirm()?->toArray(),
            'type' => $this->getType(),
            ...$url,
        ];
    }

    /**
     * Get the fallback method
     */
    protected function defaultMethod(): string
    {
        return $this->hasAction() ? Request::METHOD_POST : Request::METHOD_GET;
    }

    /**
     * Provide a selection of default dependencies for evaluation by name.
     *
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
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
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        return match ($parameterType) {
            self::class => [$this],
            Confirm::class => [$this->newConfirm()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
