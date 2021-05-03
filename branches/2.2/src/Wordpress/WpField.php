<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;
use Pollen\Field\FieldManagerInterface;
use Pollen\Field\Drivers\FileJsDriver as BaseFileJsDriver;
use Pollen\Field\Drivers\SuggestDriver as BaseSuggestDriver;
use tiFy\Wordpress\Field\Drivers\FileJsDriver;
use tiFy\Wordpress\Field\Drivers\FindpostsDriver;
use tiFy\Wordpress\Field\Drivers\MediaFileDriver;
use tiFy\Wordpress\Field\Drivers\MediaImageDriver;
use tiFy\Wordpress\Field\Drivers\SuggestDriver;

class WpField
{
    use ContainerProxy;

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
     * @var FieldManagerInterface
     */
    protected $field;

    /**
     * @param FieldManagerInterface $field
     * @param Container $container
     */
    public function __construct(FieldManagerInterface $field, Container $container)
    {
        $this->field = $field;
        $this->setContainer($container);

        $this->registerDrivers();
        $this->registerOverride();

        foreach ($this->drivers as $name => $alias) {
            $this->field->register($name, $alias);
        }
    }

    /**
     * Déclaration des pilotes spécifiques à Wordpress.
     *
     * @return void
     */
    public function registerDrivers(): void
    {
        $this->containerAdd(
            FindpostsDriver::class,
            function () {
                return new FindpostsDriver($this->containerGet(FieldManagerInterface::class));
            }
        );

        $this->containerAdd(
            MediaFileDriver::class,
            new MediaFileDriver($this->containerGet(FieldManagerInterface::class))
        );

        $this->containerAdd(
            MediaImageDriver::class,
            new MediaImageDriver($this->containerGet(FieldManagerInterface::class))
        );
    }

    /**
     * Déclaration des surchages de pilote de champs.
     *
     * @return void
     */
    public function registerOverride(): void
    {
        $this->containerAdd(
            BaseFileJsDriver::class,
            function () {
                return new FileJsDriver($this->containerGet(FieldManagerInterface::class));
            }
        );

        $this->containerAdd(
            BaseSuggestDriver::class,
            function () {
                return new SuggestDriver($this->containerGet(FieldManagerInterface::class));
            }
        );
    }
}