<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

interface Handles
{
    /**
     * Decode and retrieve the actionable class.
     *
     * @param  string  $value
     * @return mixed
     */
    public static function tryFrom($value);

    /**
     * Handle the incoming action request.
     *
     * @param  \Honed\Action\Http\Requests\InvokableRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handle($request);
}
