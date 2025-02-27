<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Concerns\Allowable;
use Honed\Core\Concerns\HasExtra;
use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasRoute;
use Honed\Core\Concerns\HasType;
use Honed\Core\Contracts\Resolves;
use Honed\Core\Primitive;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @extends \Honed\Core\Primitive<string,mixed>
 */
abstract class Action extends Primitive implements Resolves
{
    use Allowable;
    use Concerns\HasAction;
    use Concerns\HasConfirm;
    use ForwardsCalls;
    use HasExtra;
    use HasIcon;
    use HasLabel;
    use HasName;
    use HasRoute;
    use HasType;

    /**
     * Create a new action instance.
     */
    public static function make(string $name, string|\Closure|null $label = null): static
    {
        return resolve(static::class)
            ->name($name)
            ->label($label ?? static::makeLabel($name));
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
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
     * Get the array representation of the action's route if applicable.
     *
     * @return array<string,mixed>|null
     */
    public function routeToArray(): ?array
    {
        if (! $this->hasRoute()) {
            return null;
        }

        return [
            'href' => $this->getRoute(),
            'method' => $this->getMethod(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(array $parameters = [], array $typed = []): static
    {
        $this->resolveLabel($parameters, $typed);
        $this->resolveIcon($parameters, $typed);
        $this->resolveExtra($parameters, $typed);
        $this->resolveRoute($parameters, $typed);
        $this->resolveConfirm($parameters, $typed);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'confirm' => [$this->confirmInstance()],
            default => [],
        };
    }

    /**
     * {@inheritdoc}
     */
    public function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        return match ($parameterType) {
            Confirm::class => [$this->confirmInstance()],
            default => [App::make($parameterType)], // Dependency injection
        };
    }
}
