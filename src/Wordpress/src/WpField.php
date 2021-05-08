<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;
use Pollen\Field\FieldManagerInterface;
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
        'file-js'     => FileJsDriver::class,
        'findposts'   => FindpostsDriver::class,
        'media-file'  => MediaFileDriver::class,
        'media-image' => MediaImageDriver::class,
        'suggest'     => SuggestDriver::class
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

        foreach ($this->drivers as $name => $alias) {
            $this->field->register($name, $alias);
        }
    }
}