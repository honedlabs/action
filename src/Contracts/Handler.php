<?php

namespace Honed\Action\Contracts;

use Illuminate\Contracts\Routing\UrlRoutable;

interface Handler extends UrlRoutable
{
    /**
     * Handle the incoming action request.
     *
     * @param  \Honed\Action\Http\Requests\InvokableRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handle($request);

    /**
     * Find a primitive class from the encoded value.
     * 
     * @param  string  $value
     * @return mixed
     */
    public static function find($value);
}