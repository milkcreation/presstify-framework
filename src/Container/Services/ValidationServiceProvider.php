<?php

declare(strict_types=1);

namespace tiFy\Container\Services;

use Pollen\Validation\Validator;
use Pollen\Validation\ValidatorInterface;
use tiFy\Container\ServiceProvider as BaseServiceProvider;

class ValidationServiceProvider extends BaseServiceProvider
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