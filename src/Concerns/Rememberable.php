<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Attributes\Remember;
use Honed\Action\Contracts\RememberUnserializeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;
use Throwable;

trait Rememberable
{
    /**
     * Whether the instance is rememberable.
     *
     * @var bool
     */
    protected $rememberable = false;

    /**
     * The data to remember.
     *
     * @var array<string, string>|null
     */
    protected $remember;

    /**
     * Set whether the instance can remember data.
     *
     * @return $this
     */
    public function rememberable(bool $value = true): static
    {
        $this->rememberable = $value;

        return $this;
    }

    /**
     * Set whether the instance cannot remember data.
     *
     * @return $this
     */
    public function notRememberable(bool $value = true): static
    {
        return $this->rememberable(! $value);
    }

    /**
     * Determine if the instance can remember data.
     */
    public function isRememberable(): bool
    {
        return $this->rememberable;
    }

    /**
     * Determine if the instance cannot remember data.
     */
    public function isNotRememberable(): bool
    {
        return ! $this->isRememberable();
    }

    /**
     * Get the data to remember.
     *
     * @return array<string, string>
     *
     * @internal
     */
    public function getRemembered(): array
    {
        if ($this->isNotRememberable()) {
            return [];
        }

        if (! isset($this->remember)) {
            return $this->remember();
        }

        return $this->remember;
    }

    /**
     * Unserialize the remembered properties and set them on the instance.
     *
     * @internal
     */
    public function setRemembered(Request $request): void
    {
        if ($this->isNotRememberable()) {
            return;
        }

        $properties = $this->getRememberedProperties();

        foreach ($properties as $property) {
            if ($request->has($property->getName())) {
                $value = $this->unserializeRemember(
                    /** @var string */
                    $request->query($property->getName())
                );

                if ($value !== null) {
                    $property->setValue($this, $value);
                }
            }
        }
    }

    /**
     * Remember the properties that are marked as remembered.
     *
     * @return array<string, string>
     *
     * @internal
     */
    protected function remember(): array
    {
        $properties = $this->getRememberedProperties();

        foreach ($properties as $property) {
            $value = $property->getValue($this);

            if ($value !== null) {
                $this->remember[$property->getName()] = base64_encode(
                    $this->serializeRemember($value)
                );
            }
        }

        return $this->remember ??= [];
    }

    /**
     * Get the properties that are marked as remembered.
     *
     * @return array<int, ReflectionProperty>
     *
     * @internal
     */
    protected function getRememberedProperties(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties();

        $remembered = [];

        foreach ($properties as $property) {
            if ($property->getAttributes(Remember::class) !== []) {
                $remembered[] = $property;
            }
        }

        return $remembered;
    }

    /**
     * Serialize the value for remembering to a request parameter.
     *
     * @throws RuntimeException
     *
     * @internal
     */
    protected function serializeRemember(mixed $value): string
    {
        return match (true) {
            // @phpstan-ignore-next-line binaryOp.invalid
            $value instanceof Model => $value::class.'|'.$value->getKey(),
            is_scalar($value) => (string) $value,
            default => throw new RuntimeException(
                'Unable to serialize the given value. You can only supply scalar values or an Eloquent model.'
            )
        };
    }

    /**
     * Unserialize the value from a request parameter.
     *
     *
     * @internal
     */
    protected function unserializeRemember(?string $data): mixed
    {
        if ($data === null) {
            return null;
        }

        $data = base64_decode($data);

        return match (true) {
            str_contains($data, '|') => $this->unserializeModel($data),
            filter_var($data, FILTER_VALIDATE_INT) !== false => (int) $data,
            filter_var($data, FILTER_VALIDATE_FLOAT) !== false => (float) $data,
            filter_var($data, FILTER_VALIDATE_BOOLEAN) !== false => (bool) $data,
            default => $data
        };
    }

    /**
     * Unserialize the model from a request parameter.
     *
     * @internal
     */
    protected function unserializeModel(string $data): ?Model
    {
        try {
            [$class, $key] = explode('|', $data);

            if (! class_exists($class) || ! is_subclass_of($class, Model::class)) {
                throw new RuntimeException(
                    "Unable to unserialize the model. The class [$class] does not exist."
                );
            }
            /** @var class-string<Model> $class */

            // @phpstan-ignore-next-line instanceof.alwaysFalse
            if ($class instanceof RememberUnserializeable) {
                $class->rememberUnserialize($key);
            }

            return $class::query()->findOrFail($key);
        } catch (Throwable $e) {
            return null;
        }
    }
}
