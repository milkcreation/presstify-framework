<?php

namespace tiFy\Field;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Contracts\Field\FieldItemInterface;
use tiFy\Field\Field;
use tiFy\Field\Button\Button;
use tiFy\Field\Checkbox\Checkbox;
use tiFy\Field\CheckboxCollection\CheckboxCollection;
use tiFy\Field\Colorpicker\Colorpicker;
use tiFy\Field\Crypted\Crypted;
use tiFy\Field\DatetimeJs\DatetimeJs;
use tiFy\Field\File\File;
use tiFy\Field\Hidden\Hidden;
use tiFy\Field\Label\Label;
use tiFy\Field\MediaFile\MediaFile;
use tiFy\Field\MediaImage\MediaImage;
use tiFy\Field\Number\Number;
use tiFy\Field\NumberJs\NumberJs;
use tiFy\Field\Password\Password;
use tiFy\Field\Radio\Radio;
use tiFy\Field\RadioCollection\RadioCollection;
use tiFy\Field\Repeater\Repeater;
use tiFy\Field\Select\Select;
use tiFy\Field\SelectJs\SelectJs;
use tiFy\Field\Submit\Submit;
use tiFy\Field\Text\Text;
use tiFy\Field\Textarea\Textarea;
use tiFy\Field\TextRemaining\TextRemaining;
use tiFy\Field\ToggleSwitch\ToggleSwitch;

class FieldServiceProvider extends AppServiceProvider
{
    /**
     * Liste des instances des éléments déclarés.
     * @var array
     */
    protected static $instances = [];

    /**
     * Liste des alias de qualification des éléments.
     * @var array
     */
    protected $aliases = [
        'field.button'              => Button::class,
        'field.checkbox'            => Checkbox::class,
        'field.checkbox-collection' => CheckboxCollection::class,
        'field.colorpicker'         => Colorpicker::class,
        'field.crypted'             => Crypted::class,
        'field.datetime-js'         => DatetimeJs::class,
        'field.file'                => File::class,
        'field.hidden'              => Hidden::class,
        'field.label'               => Label::class,
        'field.media-file'          => MediaFile::class,
        'field.media-image'         => MediaImage::class,
        'field.number'              => Number::class,
        'field.number-js'           => NumberJs::class,
        'field.password'            => Password::class,
        'field.radio'               => Radio::class,
        'field.radio-collection'    => RadioCollection::class,
        'field.repeater'            => Repeater::class,
        'field.select'              => Select::class,
        'field.select-js'           => SelectJs::class,
        'field.submit'              => Submit::class,
        'field.text'                => Text::class,
        'field.textearea'           => Textarea::class,
        'field.text-remaining'      => TextRemaining::class,
        'field.toggle-switch'       => ToggleSwitch::class,
    ];

    /**
     * Liste des éléments à déclarer.
     * @var array
     */
    protected $items = [
        Button::class,
        Checkbox::class,
        CheckboxCollection::class,
        Colorpicker::class,
        Crypted::class,
        DatetimeJs::class,
        File::class,
        Hidden::class,
        Label::class,
        MediaFile::class,
        MediaImage::class,
        Number::class,
        NumberJs::class,
        Password::class,
        Radio::class,
        RadioCollection::class,
        Repeater::class,
        Select::class,
        SelectJs::class,
        Submit::class,
        Text::class,
        Textarea::class,
        TextRemaining::class,
        ToggleSwitch::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        foreach($this->aliases as $alias => $concrete) :
            $this->getContainer()->setAlias($alias, $concrete);
        endforeach;

        $this->app->singleton(
            Field::class,
            function() {
                return new Field();
            });

        add_action(
            'after_setup_theme',
            function() {
                foreach ($this->items as $concrete) :
                    $alias = $this->getContainer()->getAlias($concrete);

                    $this->app
                        ->bind(
                            $alias,
                            $concrete
                        )
                        ->build([null, []]);
                endforeach;
            }
        );
    }

    /**
     * Définition de l'instance d'un champ déclaré.
     *
     * @param FieldItemInterface $instance Instance du champ.
     *
     * @return int
     */
    public function setInstance($instance)
    {
        if (!$instance instanceof FieldItemInterface) :
            return 0;
        endif;

        $concrete = class_info($instance)->getName();
        $alias = $this->getContainer()->getAlias($concrete);

        $count = empty(self::$instances[$alias]) ? 0 : count(self::$instances[$alias]);

        self::$instances[$alias][] = $instance;

        return $count;
    }
}