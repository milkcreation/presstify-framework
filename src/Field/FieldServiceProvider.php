<?php

declare(strict_types=1);

namespace tiFy\Field;

use tiFy\Container\ServiceProvider;
use tiFy\Field\Contracts\FieldContract;
use tiFy\Field\Drivers\ButtonDriver;
use tiFy\Field\Drivers\CheckboxDriver;
use tiFy\Field\Drivers\CheckboxCollectionDriver;
use tiFy\Field\Drivers\ColorpickerDriver;
use tiFy\Field\Drivers\DatepickerDriver;
use tiFy\Field\Drivers\DatetimeJsDriver;
use tiFy\Field\Drivers\FileDriver;
use tiFy\Field\Drivers\FileJsDriver;
use tiFy\Field\Drivers\HiddenDriver;
use tiFy\Field\Drivers\LabelDriver;
use tiFy\Field\Drivers\NumberDriver;
use tiFy\Field\Drivers\NumberJsDriver;
use tiFy\Field\Drivers\PasswordDriver;
use tiFy\Field\Drivers\PasswordJsDriver;
use tiFy\Field\Drivers\RadioDriver;
use tiFy\Field\Drivers\RadioCollectionDriver;
use tiFy\Field\Drivers\RepeaterDriver;
use tiFy\Field\Drivers\RequiredDriver;
use tiFy\Field\Drivers\SelectDriver;
use tiFy\Field\Drivers\SelectImageDriver;
use tiFy\Field\Drivers\SelectJsDriver;
use tiFy\Field\Drivers\SubmitDriver;
use tiFy\Field\Drivers\SuggestDriver;
use tiFy\Field\Drivers\TextDriver;
use tiFy\Field\Drivers\TextareaDriver;
use tiFy\Field\Drivers\TextRemainingDriver;
use tiFy\Field\Drivers\TinymceDriver;
use tiFy\Field\Drivers\ToggleSwitchDriver;
use tiFy\Support\Proxy\View;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        FieldContract::class,
        ButtonDriver::class,
        CheckboxDriver::class,
        CheckboxCollectionDriver::class,
        ColorpickerDriver::class,
        FileDriver::class,
        FileJsDriver::class,
        DatepickerDriver::class,
        DatetimeJsDriver::class,
        HiddenDriver::class,
        LabelDriver::class,
        NumberDriver::class,
        NumberJsDriver::class,
        PasswordDriver::class,
        PasswordJsDriver::class,
        RadioDriver::class,
        RadioCollectionDriver::class,
        RepeaterDriver::class,
        RequiredDriver::class,
        SelectDriver::class,
        SelectImageDriver::class,
        SelectJsDriver::class,
        SubmitDriver::class,
        SuggestDriver::class,
        TextDriver::class,
        TextareaDriver::class,
        TextRemainingDriver::class,
        TinymceDriver::class,
        ToggleSwitchDriver::class,
        'field.view-engine',
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            FieldContract::class,
            function () {
                return new Field(config('field', []), $this->getContainer());
            }
        );

        $this->registerDrivers();
        $this->registerViewEngine();
    }

    /**
     * Déclaration des pilotes par défaut.
     *
     * @return void
     */
    public function registerDrivers(): void
    {
        $this->getContainer()->add(
            ButtonDriver::class,
            function () {
                return new ButtonDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            CheckboxDriver::class,
            function () {
                return new CheckboxDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            CheckboxCollectionDriver::class,
            function () {
                return new CheckboxCollectionDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            ColorpickerDriver::class,
            function () {
                return new ColorpickerDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            DatepickerDriver::class,
            function () {
                return new DatepickerDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            DatetimeJsDriver::class,
            function () {
                return new DatetimeJsDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            FileDriver::class,
            function () {
                return new FileDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            FileJsDriver::class,
            function () {
                return new FileJsDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            HiddenDriver::class,
            function () {
                return new HiddenDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            LabelDriver::class,
            function () {
                return new LabelDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            NumberDriver::class,
            function () {
                return new NumberDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            NumberJsDriver::class,
            function () {
                return new NumberJsDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            PasswordDriver::class,
            function () {
                return new PasswordDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            PasswordJsDriver::class,
            function () {
                return new PasswordJsDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            RadioDriver::class,
            function () {
                return new RadioDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            RadioCollectionDriver::class,
            function () {
                return new RadioCollectionDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            RepeaterDriver::class,
            function () {
                return new RepeaterDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            RequiredDriver::class,
            function () {
                return new RequiredDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            SelectDriver::class,
            function () {
                return new SelectDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            SelectImageDriver::class,
            function () {
                return new SelectImageDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            SelectJsDriver::class,
            function () {
                return new SelectJsDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            SubmitDriver::class,
            function () {
                return new SubmitDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            SuggestDriver::class,
            function () {
                return new SuggestDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            TextDriver::class,
            function () {
                return new TextDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            TextareaDriver::class,
            function () {
                return new TextareaDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            TextRemainingDriver::class,
            function () {
                return new TextRemainingDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            TinymceDriver::class,
            function () {
                return new TinymceDriver($this->getContainer()->get(FieldContract::class));
            }
        );
        $this->getContainer()->add(
            ToggleSwitchDriver::class,
            function () {
                return new ToggleSwitchDriver($this->getContainer()->get(FieldContract::class));
            }
        );
    }

    /**
     * Déclaration du moteur d'affichage.
     *
     * @return void
     */
    public function registerViewEngine(): void
    {
        $this->getContainer()->add(
            'field.view-engine',
            function () {
                return View::getPlatesEngine();
            }
        );
    }
}