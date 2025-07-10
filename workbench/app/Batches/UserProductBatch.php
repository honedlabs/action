<?php

declare(strict_types=1);

namespace Workbench\App\Batches;

use Honed\Action\Attributes\Remember;
use Honed\Action\Batch;
use Honed\Action\Operations\PageOperation;
use Workbench\App\Models\User;

class UserProductBatch extends Batch
{
    /**
     * The user to remember.
     *
     * @var User|null
     */
    #[Remember]
    protected $user;

    /**
     * The scalar to remember.
     *
     * @var scalar
     */
    #[Remember]
    protected $scalar = 1;

    /**
     * Set the user.
     *
     * @return $this
     */
    public function user(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the user.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set the scalar.
     *
     * @param  scalar  $scalar
     * @return $this
     */
    public function scalar($scalar): static
    {
        $this->scalar = $scalar;

        return $this;
    }

    /**
     * Get the scalar.
     *
     * @return scalar
     */
    public function getScalar(): mixed
    {
        return $this->scalar;
    }

    /**
     * Define the batch.
     *
     * @return $this
     */
    protected function definition(): static
    {
        return $this
            ->rememberable()
            ->operations([
                PageOperation::make('show', 'Show a product'),
            ]);
    }
}
