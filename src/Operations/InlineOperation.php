<?php

declare(strict_types=1);

namespace Honed\Action\Operations;

use Honed\Core\Concerns\CanBeDefault;
use Honed\Core\Concerns\HasRecord;
use Illuminate\Database\Eloquent\Model;

class InlineOperation extends Operation
{
    use CanBeDefault;
    use HasRecord;

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    protected function representation(): array
    {
        return [
            ...parent::representation(),
            'default' => $this->isDefault(),
        ];
    }

    /**
     * Provide a selection of default dependencies for evaluation by name.
     *
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'model', 'record', 'row' => [$this->getRecord()],
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
        $record = $this->getRecord();

        if (! $record instanceof Model) {
            return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
        }

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
