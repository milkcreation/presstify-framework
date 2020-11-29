<?php declare(strict_types=1);

namespace tiFy\Wordpress\Field;

use Exception;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Field\Colorpicker as ColorpickerContract;
use tiFy\Contracts\Field\Field as Manager;
use tiFy\Contracts\Field\DatetimeJs as DatetimeJsContract;
use tiFy\Contracts\Field\FileJs as FileJsContract;
use tiFy\Contracts\Field\NumberJs as NumberJsContract;
use tiFy\Contracts\Field\PasswordJs as PasswordJsContract;
use tiFy\Contracts\Field\Repeater as RepeaterContract;
use tiFy\Contracts\Field\SelectImage as SelectImageContract;
use tiFy\Contracts\Field\SelectJs as SelectJsContract;
use tiFy\Contracts\Field\Suggest as SuggestContract;
use tiFy\Contracts\Field\TextRemaining as TextRemainingContract;
use tiFy\Contracts\Field\Tinymce as TinymceContract;
use tiFy\Contracts\Field\ToggleSwitch as ToggleSwitchContract;
use tiFy\Wordpress\Contracts\Field\Findposts as FindpostsContract;
use tiFy\Wordpress\Contracts\Field\MediaFile as MediaFileContract;
use tiFy\Wordpress\Contracts\Field\MediaImage as MediaImageContract;
use tiFy\Wordpress\Field\Driver\Colorpicker\Colorpicker;
use tiFy\Wordpress\Field\Driver\DatetimeJs\DatetimeJs;
use tiFy\Wordpress\Field\Driver\FileJs\FileJs;
use tiFy\Wordpress\Field\Driver\Findposts\Findposts;
use tiFy\Wordpress\Field\Driver\MediaFile\MediaFile;
use tiFy\Wordpress\Field\Driver\MediaImage\MediaImage;
use tiFy\Wordpress\Field\Driver\NumberJs\NumberJs;
use tiFy\Wordpress\Field\Driver\PasswordJs\PasswordJs;
use tiFy\Wordpress\Field\Driver\Repeater\Repeater;
use tiFy\Wordpress\Field\Driver\SelectImage\SelectImage;
use tiFy\Wordpress\Field\Driver\SelectJs\SelectJs;
use tiFy\Wordpress\Field\Driver\Suggest\Suggest;
use tiFy\Wordpress\Field\Driver\TextRemaining\TextRemaining;
use tiFy\Wordpress\Field\Driver\Tinymce\Tinymce;
use tiFy\Wordpress\Field\Driver\ToggleSwitch\ToggleSwitch;

class Field
{
    /**
     * Instance du conteneur d'injection de dépendances.
     * @var Container
     */
    protected $container;

    /**
     * Définition des pilotes spécifiques à Wordpress.
     * @var array
     */
    protected $drivers = [
        'findposts'   => FindpostsContract::class,
        'media-file'  => MediaFileContract::class,
        'media-image' => MediaImageContract::class,
    ];

    /**
     * Instance du gestionnaire des champs.
     * @var Manager
     */
    protected $manager;

    /**
     * @param Manager $manager Instance du gestionnaire des champs.
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->container = $this->manager->getContainer();

        $this->registerDrivers();
        $this->registerOverride();

        $this->manager->boot();
        foreach ($this->drivers as $name => $alias) {
            $this->manager->register($name, $this->getContainer()->get($alias));
        }
    }

    /**
     * Récupération du conteneur d'injection de dépendance.
     *
     * @return Container
     */
    protected function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Déclaration des pilotes spécifiques à Wordpress.
     *
     * @return void
     */
    public function registerDrivers(): void
    {
        $this->getContainer()->add(FindpostsContract::class, function () {
            return new Findposts();
        });

        $this->getContainer()->add(MediaFileContract::class, function () {
            return new MediaFile();
        });

        $this->getContainer()->add(MediaImageContract::class, function () {
            return new MediaImage();
        });
    }

    /**
     * Déclaration des controleurs de surchage des champs.
     *
     * @return void
     */
    public function registerOverride(): void
    {
        $this->getContainer()->add(ColorpickerContract::class, function () {
            return new Colorpicker();
        });

        $this->getContainer()->add(DatetimeJsContract::class, function () {
            return new DatetimeJs();
        });

        $this->getContainer()->add(FileJsContract::class, function () {
            return new FileJs();
        });

        $this->getContainer()->add(NumberJsContract::class, function () {
            return new NumberJs();
        });

        $this->getContainer()->add(PasswordJsContract::class, function () {
            return new PasswordJs();
        });

        $this->getContainer()->add(RepeaterContract::class, function () {
            return new Repeater();
        });

        $this->getContainer()->add(SelectImageContract::class, function () {
            return new SelectImage();
        });

        $this->getContainer()->add(SelectJsContract::class, function () {
            return new SelectJs();
        });

        $this->getContainer()->add(SuggestContract::class, function () {
            return new Suggest();
        });

        $this->getContainer()->add(TextRemainingContract::class, function () {
            return new TextRemaining();
        });

        $this->getContainer()->add(TinymceContract::class, function () {
            return new Tinymce();
        });

        $this->getContainer()->add(ToggleSwitchContract::class, function () {
            return new ToggleSwitch();
        });
    }
}