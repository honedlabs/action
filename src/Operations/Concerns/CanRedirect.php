<?php

declare(strict_types=1);

namespace Honed\Action\Operations\Concerns;

use Closure;
use Illuminate\Support\Str;

trait CanRedirect
{
    /**
     * The redirect for when the operation is executed.
     *
     * @var string|(Closure(mixed...):\Illuminate\Http\RedirectResponse)|null
     */
    protected $redirect;

    /**
     * Set the redirect for when the operation is executed.
     *
     * @param  string|(Closure(mixed...):\Illuminate\Http\RedirectResponse)|null  $redirect
     * @return $this
     */
    public function redirect($redirect)
    {
        $this->redirect = $redirect;

        return $this;
    }

    /**
     * Get the redirect for when the operation is executed.
     *
     * @return string|(Closure(mixed...):\Illuminate\Http\RedirectResponse)|null
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * Determine if the operation has a redirect.
     *
     * @return bool
     */
    public function hasRedirect()
    {
        return isset($this->redirect);
    }

    /**
     * Call the redirect for when the operation is executed.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callRedirect()
    {
        $redirect = $this->getRedirect();

        /** @var \Illuminate\Http\RedirectResponse */
        return match (true) {
            ! $redirect => back(),
            $redirect instanceof Closure => $this->evaluate($redirect),
            Str::startsWith($redirect, ['http://', 'https://', '/']) => redirect($redirect),
            default => to_route($redirect)
        };
    }
}
