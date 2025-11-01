<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TInput of mixed = array<string, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 *
 * @extends \Honed\Action\Actions\ModelAction<TModel, TInput>
 */
class UpdateAction extends ModelAction
{
    /**
     * Act on the model.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @return TModel
     */
    public function act(Model $model, $input): Model
    {
        $model->update($input);

        return $model;
    }
}
