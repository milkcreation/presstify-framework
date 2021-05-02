<?php

declare(strict_types=1);

namespace tiFy\Validation;

use Pollen\Validation\ValidatorInterface;
use tiFy\Container\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        ValidatorInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(ValidatorInterface::class, function () {
            return new Validator();
        });
    }
}