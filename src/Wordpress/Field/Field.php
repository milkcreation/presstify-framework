<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Field;

use Psr\Container\ContainerInterface as Container;
use tiFy\Field\Contracts\FieldContract;
use tiFy\Support\Concerns\ContainerAwareTrait;
use tiFy\Wordpress\Field\Drivers\FileJsDriver;
use tiFy\Wordpress\Field\Drivers\FindpostsDriver;
use tiFy\Wordpress\Field\Drivers\MediaFileDriver;
use tiFy\Wordpress\Field\Drivers\MediaImageDriver;
use tiFy\Wordpress\Field\Drivers\SuggestDriver;

class Field
{
    use ContainerAwareTrait;

    /**
     * Définition des pilotes spécifiques à Wordpress.
     * @var array
     */
    protected $drivers = [
        'findposts'   => FindpostsDriver::class,
        'media-file'  => MediaFileDriver::class,
        'media-image' => MediaImageDriver::class,
    ];

    /**
     * Instance du gestionnaire des champs.
     * @var FieldContract
     */
    protected $fieldManager;

    /**
     * @param FieldContract $fieldManager Instance du gestionnaire des champs.
     * @param Container $container
     */
    public function __construct(FieldContract $fieldManager, Container $container)
    {
        $this->fieldManager = $fieldManager;
        $this->setContainer($container);

        $this->registerDrivers();
        $this->registerOverride();

        $this->fieldManager->boot();
        foreach ($this->drivers as $name => $alias) {
            $this->fieldManager->register($name, $this->getContainer()->get($alias));
        }
    }

    /**
     * Déclaration des pilotes spécifiques à Wordpress.
     *
     * @return void
     */
    public function registerDrivers(): void
    {
        $this->getContainer()->add(
            FindpostsDriver::class,
            function () {
                return new FindpostsDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            MediaFileDriver::class,
            new MediaFileDriver($this->getContainer()->get(FieldContract::class))
        );
        $this->getContainer()->add(
            MediaImageDriver::class,
            new MediaImageDriver($this->getContainer()->get(FieldContract::class))
        );
    }

    /**
     * Déclaration des controleurs de surchage des champs.
     *
     * @return void
     */
    public function registerOverride(): void
    {
        $this->getContainer()->add(
            FileJsDriver::class,
            function () {
                return new FileJsDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            SuggestDriver::class,
            function () {
                return new SuggestDriver($this->getContainer()->get(FieldContract::class));
            }
        );
    }
}