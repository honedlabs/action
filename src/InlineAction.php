<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Contracts\HasHandler;
use Honed\Core\Concerns\IsDefault;
use Illuminate\Database\Eloquent\Model;

class InlineAction extends Action
{
    use IsDefault;

    public function setUp(): void
    {
        $this->type(Creator::Inline);
    }

    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'default' => $this->isDefault(),
        ]);
    }

    /**
     * Execute the action handler using the provided data.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $record
     * 
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\RedirectResponse|void
     */
    public function execute($record)
    {
        if (! $this->hasAction()) {
            return;
        }

        return $this instanceof HasHandler
            ? $this->callHandler($record)
            : $this->callAction($record);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $record
     * 
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\RedirectResponse|void
     */
    protected function callHandler($record)
    {
        return \call_user_func([$this, 'handle'], $record); // @phpstan-ignore-line
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $record
     * 
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\RedirectResponse|void
     */
    protected function callAction($record)
    {
        [$model, $singular] = $this->getParameterNames($record);

        $named = [
            'model' => $model,
            'record' => $record,
            $singular => $record,
        ];

        $typed = [
            Model::class => $record,
            $model::class => $record,
        ];

        return $this->evaluate($this->getAction(), $named, $typed);
    }
}
