<?php declare(strict_types=1);

namespace tiFy\Field;

use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Field\Button as ButtonContract;
use tiFy\Contracts\Field\Checkbox as CheckboxContract;
use tiFy\Contracts\Field\CheckboxCollection as CheckboxCollectionContract;
use tiFy\Contracts\Field\Colorpicker as ColorpickerContract;
use tiFy\Contracts\Field\Datepicker as DatepickerContract;
use tiFy\Contracts\Field\DatetimeJs as DatetimeJsContract;
use tiFy\Contracts\Field\File as FileContract;
use tiFy\Contracts\Field\FileJs as FileJsContract;
use tiFy\Contracts\Field\Hidden as HiddenContract;
use tiFy\Contracts\Field\Label as LabelContract;
use tiFy\Contracts\Field\Number as NumberContract;
use tiFy\Contracts\Field\NumberJs as NumberJsContract;
use tiFy\Contracts\Field\Password as PasswordContract;
use tiFy\Contracts\Field\PasswordJs as PasswordJsContract;
use tiFy\Contracts\Field\Radio as RadioContract;
use tiFy\Contracts\Field\RadioCollection as RadioCollectionContract;
use tiFy\Contracts\Field\Repeater as RepeaterContract;
use tiFy\Contracts\Field\Required as RequiredContract;
use tiFy\Contracts\Field\Select as SelectContract;
use tiFy\Contracts\Field\SelectImage as SelectImageContract;
use tiFy\Contracts\Field\SelectJs as SelectJsContract;
use tiFy\Contracts\Field\Submit as SubmitContract;
use tiFy\Contracts\Field\Suggest as SuggestContract;
use tiFy\Contracts\Field\Text as TextContract;
use tiFy\Contracts\Field\Textarea as TextareaContract;
use tiFy\Contracts\Field\TextRemaining as TextRemainingContract;
use tiFy\Contracts\Field\Tinymce as TinymceContract;
use tiFy\Contracts\Field\ToggleSwitch as ToggleSwitchContract;
use tiFy\Field\Driver\Button\Button;
use tiFy\Field\Driver\Checkbox\Checkbox;
use tiFy\Field\Driver\CheckboxCollection\CheckboxCollection;
use tiFy\Field\Driver\Colorpicker\Colorpicker;
use tiFy\Field\Driver\Datepicker\Datepicker;
use tiFy\Field\Driver\DatetimeJs\DatetimeJs;
use tiFy\Field\Driver\File\File;
use tiFy\Field\Driver\FileJs\FileJs;
use tiFy\Field\Driver\Hidden\Hidden;
use tiFy\Field\Driver\Label\Label;
use tiFy\Field\Driver\Number\Number;
use tiFy\Field\Driver\NumberJs\NumberJs;
use tiFy\Field\Driver\Password\Password;
use tiFy\Field\Driver\PasswordJs\PasswordJs;
use tiFy\Field\Driver\Radio\Radio;
use tiFy\Field\Driver\RadioCollection\RadioCollection;
use tiFy\Field\Driver\Repeater\Repeater;
use tiFy\Field\Driver\Required\Required;
use tiFy\Field\Driver\Select\Select;
use tiFy\Field\Driver\SelectImage\SelectImage;
use tiFy\Field\Driver\SelectJs\SelectJs;
use tiFy\Field\Driver\Submit\Submit;
use tiFy\Field\Driver\Suggest\Suggest;
use tiFy\Field\Driver\Text\Text;
use tiFy\Field\Driver\Textarea\Textarea;
use tiFy\Field\Driver\TextRemaining\TextRemaining;
use tiFy\Field\Driver\Tinymce\Tinymce;
use tiFy\Field\Driver\ToggleSwitch\ToggleSwitch;
use tiFy\Support\Proxy\View;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        'field',
        'field.view-engine',
        ButtonContract::class,
        CheckboxContract::class,
        CheckboxCollectionContract::class,
        ColorpickerContract::class,
        FileContract::class,
        FileJsContract::class,
        DatepickerContract::class,
        DatetimeJsContract::class,
        HiddenContract::class,
        LabelContract::class,
        NumberContract::class,
        NumberJsContract::class,
        PasswordContract::class,
        PasswordJsContract::class,
        RadioContract::class,
        RadioCollectionContract::class,
        RepeaterContract::class,
        RequiredContract::class,
        SelectContract::class,
        SelectImageContract::class,
        SelectJsContract::class,
        SubmitContract::class,
        SuggestContract::class,
        TextContract::class,
        TextareaContract::class,
        TextRemainingContract::class,
        TinymceContract::class,
        ToggleSwitchContract::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('field', function () {
            return new Field(config('field', []), $this->getContainer());
        });

        $this->registerDefaultDrivers();

        $this->registerViewEngine();
    }

    /**
     * Déclaration des pilotes par défaut.
     *
     * @return void
     */
    public function registerDefaultDrivers(): void
    {
        $this->getContainer()->add(ButtonContract::class, function () {
            return new Button();
        });

        $this->getContainer()->add(CheckboxContract::class, function () {
            return new Checkbox();
        });

        $this->getContainer()->add(CheckboxCollectionContract::class, function () {
            return new CheckboxCollection();
        });

        $this->getContainer()->add(ColorpickerContract::class, function () {
            return new Colorpicker();
        });

        $this->getContainer()->add(DatepickerContract::class, function (): DatepickerContract {
            return new Datepicker();
        });

        $this->getContainer()->add(DatetimeJsContract::class, function () {
            return new DatetimeJs();
        });

        $this->getContainer()->add(FileContract::class, function () {
            return new File();
        });

        $this->getContainer()->add(FileJsContract::class, function () {
            return new FileJs();
        });

        $this->getContainer()->add(HiddenContract::class, function () {
            return new Hidden();
        });

        $this->getContainer()->add(LabelContract::class, function () {
            return new Label();
        });

        $this->getContainer()->add(NumberContract::class, function () {
            return new Number();
        });

        $this->getContainer()->add(NumberJsContract::class, function () {
            return new NumberJs();
        });

        $this->getContainer()->add(PasswordContract::class, function () {
            return new Password();
        });

        $this->getContainer()->add(PasswordJsContract::class, function () {
            return new PasswordJs();
        });

        $this->getContainer()->add(RadioContract::class, function () {
            return new Radio();
        });

        $this->getContainer()->add(RadioCollectionContract::class, function () {
            return new RadioCollection();
        });

        $this->getContainer()->add(RepeaterContract::class, function () {
            return new Repeater();
        });

        $this->getContainer()->add(RequiredContract::class, function () {
            return new Required();
        });

        $this->getContainer()->add(SelectContract::class, function () {
            return new Select();
        });

        $this->getContainer()->add(SelectImageContract::class, function () {
            return new SelectImage();
        });

        $this->getContainer()->add(SelectJsContract::class, function () {
            return new SelectJs();
        });

        $this->getContainer()->add(SubmitContract::class, function () {
            return new Submit();
        });

        $this->getContainer()->add(SuggestContract::class, function () {
            return new Suggest();
        });

        $this->getContainer()->add(TextContract::class, function () {
            return new Text();
        });

        $this->getContainer()->add(TextareaContract::class, function () {
            return new Textarea();
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

    /**
     * Déclaration du moteur d'affichage.
     *
     * @return void
     */
    public function registerViewEngine(): void
    {
        $this->getContainer()->add('field.view-engine', function () {
            return View::getPlatesEngine();
        });
    }
}