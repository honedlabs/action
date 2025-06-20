<?php

declare(strict_types=1);

namespace Honed\Action;

use Closure;
use Honed\Core\Concerns\HasRecord;
use Honed\Core\Primitive;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends \Honed\Core\Primitive<string, mixed>
 */
class Confirm extends Primitive
{
    use HasRecord;

    public const CONSTRUCTIVE = 'constructive';

    public const DESTRUCTIVE = 'destructive';

    public const INFORMATIVE = 'informative';

    /**
     * The title of the confirm.
     *
     * @var string|Closure(mixed...):string|null
     */
    protected $title;

    /**
     * The description of the confirm
     *
     * @var string|Closure(mixed...):string|null
     */
    protected $description;

    /**
     * The intent of the confirm.
     *
     * @var string|null
     */
    protected $intent;

    /**
     * The message to display on the submit button.
     *
     * @var string|Closure(mixed...):string
     */
    protected $submit = 'Confirm';

    /**
     * The message to display on the dismiss button.
     *
     * @var string|Closure(mixed...):string
     */
    protected $dismiss = 'Cancel';

    /**
     * Create a new confirm instance.
     *
     * @param  string|Closure(mixed...):string|null  $title
     * @param  string|Closure(mixed...):string|null  $description
     * @return static
     */
    public static function make($title = null, $description = null)
    {
        return resolve(static::class)
            ->title($title)
            ->description($description);
    }

    /**
     * Set the title of the confirm.
     *
     * @param  string|Closure(mixed...):string|null  $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the title of the confirm.
     *
     * @return string|null
     */
    public function getTitle()
    {
        /** @var string|null */
        return $this->evaluate($this->title);
    }

    /**
     * Set the description of the confirm.
     *
     * @param  string|Closure(mixed...):string|null  $description
     * @return $this
     */
    public function description($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description of the confirm.
     *
     * @return string|null
     */
    public function getDescription()
    {
        /** @var string|null */
        return $this->evaluate($this->description);
    }

    /**
     * Set the intent of the confirm.
     *
     * @param  string|null  $intent
     * @return $this
     */
    public function intent($intent)
    {
        $this->intent = $intent;

        return $this;
    }

    /**
     * Set the intent as constructive.
     *
     * @return $this
     */
    public function constructive()
    {
        return $this->intent(self::CONSTRUCTIVE);
    }

    /**
     * Set the intent as destructive.
     *
     * @return $this
     */
    public function destructive()
    {
        return $this->intent(self::DESTRUCTIVE);
    }

    /**
     * Set the intent as informative.
     *
     * @return $this
     */
    public function informative()
    {
        return $this->intent(self::INFORMATIVE);
    }

    /**
     * Get the intent of the confirm.
     *
     * @return string|null
     */
    public function getIntent()
    {
        return $this->intent;
    }

    /**
     * Set the submit message for the confirm.
     *
     * @param  string|Closure(mixed...):string  $submit
     * @return $this
     */
    public function submit($submit)
    {
        $this->submit = $submit;

        return $this;
    }

    /**
     * Get the submit message for the confirm.
     *
     * @return string
     */
    public function getSubmit()
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
    public function dismiss($dismiss)
    {
        $this->dismiss = $dismiss;

        return $this;
    }

    /**
     * Get the dismiss message for the confirm.
     *
     * @return string
     */
    public function getDismiss()
    {
        /** @var string */
        return $this->evaluate($this->dismiss);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
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
