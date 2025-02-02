<?php

declare(strict_types=1);

namespace Honed\Action;

use Illuminate\Support\Traits\ForwardsCalls;

class BulkAction extends Action
{
    use Concerns\HasBulkActions;
    use Concerns\HasConfirm;
    use Concerns\KeepsSelected;

    protected $type = Creator::Bulk;

    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'action' => $this->hasAction(),
            'keepSelected' => $this->keepsSelected(),
            'confirm' => $this->getConfirm(),
        ]);
    }

    /**
     * @param  array<string,mixed>|\Illuminate\Database\Eloquent\Model  $parameters
     * @param  array<string,mixed>  $typed
     */
    public function resolve($parameters = [], $typed = []): static
    {
        // $this->resolveConfirm($parameters, $typed);

        return parent::resolve($parameters, $typed);
    }

}
