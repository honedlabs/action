<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\UpdateRelatedAction;

/**
 * @template TModel of \Workbench\App\Models\Product
 * @template TRelated of \Workbench\App\Models\User
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\BelongsToMany
 *
 * @extends \Honed\Action\Actions\UpdateRelatedAction<TModel, TRelated, TRelation>
 */
class UpdateUser extends UpdateRelatedAction
{
    /**
     * {@inheritdoc}
     */
    public function relationship(): string
    {
        return 'user';
    }
}
