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
     * Get the type of the operation.
     */
    protected function type(): string
    {
        return self::INLINE;
    }

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
     * @param  string  $parameterName
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName($parameterName)
    {
        return match ($parameterName) {
            'model', 'record', 'row' => [$this->getRecord()],
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
