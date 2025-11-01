<?php

declare(strict_types=1);

namespace Honed\Action;

use Closure;
use Honed\Core\Concerns\HasRecord;
use Honed\Core\Contracts\NullsAsUndefined;
use Honed\Core\Primitive;
use Illuminate\Database\Eloquent\Model;

class Confirm extends Primitive implements NullsAsUndefined
{
    use HasRecord;

    public const TITLE = 'Confirmation required';

    public const DESCRIPTION = 'Are you sure you want to perform this action?';

    public const SUBMIT = 'Confirm';

    public const DISMISS = 'Cancel';

    public const CONSTRUCTIVE = 'constructive';

    public const DESTRUCTIVE = 'destructive';

    public const INFORMATIVE = 'informative';

    /**
     * The title of the confirm.
     *
     * @var string|Closure(mixed...):string
     */
    protected string|Closure $title = self::TITLE;

    /**
     * The description of the confirm
     *
     * @var string|Closure(mixed...):string
     */
    protected string|Closure $description = self::DESCRIPTION;

    /**
     * The intent of the confirm.
     */
    protected ?string $intent = null;

    /**
     * The message to display on the submit button.
     *
     * @var string|Closure(mixed...):string
     */
    protected string|Closure $submit = self::SUBMIT;

    /**
     * The message to display on the dismiss button.
     *
     * @var string|Closure(mixed...):string
     */
    protected string|Closure $dismiss = self::DISMISS;

    /**
     * Create a new confirm instance.
     *
     * @param  string|Closure(mixed...):string  $title
     * @param  string|Closure(mixed...):string  $description
     * @return static
     */
    public static function make(
        string|Closure $title = self::TITLE,
        string|Closure $description = self::DESCRIPTION
    ) {
        return resolve(static::class)
            ->title($title)
            ->description($description);
    }

    /**
     * Set the title of the confirm.
     *
     * @param  string|Closure(mixed...):string  $title
     * @return $this
     */
    public function title(string|Closure $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the title of the confirm.
     */
    public function getTitle(): string
    {
        /** @var string */
        return $this->evaluate($this->title);
    }

    /**
     * Set the description of the confirm.
     *
     * @param  string|Closure(mixed...):string  $description
     * @return $this
     */
    public function description(string|Closure $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description of the confirm.
     */
    public function getDescription(): ?string
    {
        /** @var string */
        return $this->evaluate($this->description);
    }

    /**
     * Set the intent of the confirm.
     *
     * @return $this
     */
    public function intent(?string $intent): static
    {
        $this->intent = $intent;

        return $this;
    }

    /**
     * Set the intent as constructive.
     *
     * @return $this
     */
    public function constructive(): static
    {
        return $this->intent(self::CONSTRUCTIVE);
    }

    /**
     * Set the intent as destructive.
     *
     * @return $this
     */
    public function destructive(): static
    {
        return $this->intent(self::DESTRUCTIVE);
    }

    /**
     * Set the intent as informative.
     *
     * @return $this
     */
    public function informative(): static
    {
        return $this->intent(self::INFORMATIVE);
    }

    /**
     * Get the intent of the confirm.
     */
    public function getIntent(): ?string
    {
        return $this->intent;
    }

    /**
     * Set the submit message for the confirm.
     *
     * @param  string|Closure(mixed...):string  $submit
     * @return $this
     */
    public function submit(string|Closure $submit): static
    {
        $this->submit = $submit;

        return $this;
    }

    /**
     * Get the submit message for the confirm.
     */
    public function getSubmit(): string
    {
        /** @var string */
        return $this->evaluate($this->submit);
    }

    /**
     * Set the dismiss message for the confirm.
     *
     * @param  string|Closure(mixed...):string  $dismiss
     * @return $this
     */
    public function dismiss(string|Closure $dismiss): static
    {
        $this->dismiss = $dismiss;

        return $this;
    }

    /**
     * Get the dismiss message for the confirm.
     */
    public function getDismiss(): string
    {
        /** @var string */
        return $this->evaluate($this->dismiss);
    }

    /**
     * Get the representation of the instance.
     *
     * @return array<string, mixed>
     */
    protected function representation(): array
    {
        $this->define();

        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'dismiss' => $this->getDismiss(),
            'submit' => $this->getSubmit(),
            'intent' => $this->getIntent(),
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
     * @param  class-string  $parameterType
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
