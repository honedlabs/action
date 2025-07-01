<?php

declare(strict_types=1);

namespace Honed\Action\Operations\Concerns;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

trait CanRedirect
{
    /**
     * The redirect for when the operation is executed.
     *
     * @var RedirectResponse|(Closure(mixed...):RedirectResponse)|null
     */
    protected RedirectResponse|Closure|null $redirect = null;

    /**
     * Determine if the parameters are a route bound.
     */
    abstract protected function implicitRoute(mixed $parameters): bool;

    /**
     * Set the redirect for when the operation is executed.
     *
     * @param  string|RedirectResponse|(Closure(mixed...):RedirectResponse)|null  $value
     * @param  array<string, mixed>  $parameters
     * @return $this
     */
    public function redirect(string|RedirectResponse|Closure|null $value, mixed $parameters = []): static
    {
        $this->redirect = match (true) {
            ! $value => null,
            $value instanceof Closure => $value,
            $value instanceof RedirectResponse => $value,
            $this->implicitRoute($parameters) => fn ($record = null) => to_route($value, $record),
            Str::startsWith($value, ['http://', 'https://', '/', '#']) => redirect($value),
            default => to_route($value, $parameters),
        };

        return $this;
    }

    /**
     * Get the redirect for when the operation is executed.
     *
     * @return RedirectResponse|(Closure(mixed...):RedirectResponse)|null
     */
    public function getRedirect(): RedirectResponse|Closure|null
    {
        return $this->redirect;
    }

    /**
     * Determine if the operation has a redirect.
     */
    public function hasRedirect(): bool
    {
        return isset($this->redirect);
    }

    /**
     * Call the redirect for when the operation is executed.
     *
     * @param  array<string, mixed>  $named
     * @param  array<class-string, mixed>  $typed
     */
    public function callRedirect(array $named = [], array $typed = []): RedirectResponse
    {
        $redirect = $this->getRedirect();

        /** @var RedirectResponse */
        return match (true) {
            ! $redirect => back(),
            $redirect instanceof Closure => $this->evaluate($redirect, $named, $typed),
            default => $redirect,
        };
    }
}
