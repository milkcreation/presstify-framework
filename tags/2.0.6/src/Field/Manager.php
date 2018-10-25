<?php

/**
 * @name Field.
 * @desc Gestion de champs de formulaire.
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Field;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use tiFy\Contracts\Field\FieldController;
use tiFy\Contracts\Field\Manager as ManagerInterface;
use tiFy\Field\Button\Button;
use tiFy\Field\Checkbox\Checkbox;
use tiFy\Field\CheckboxCollection\CheckboxCollection;
use tiFy\Field\Colorpicker\Colorpicker;
use tiFy\Field\Crypted\Crypted;
use tiFy\Field\DatetimeJs\DatetimeJs;
use tiFy\Field\File\File;
use tiFy\Field\Findposts\Findposts;
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
use tiFy\Field\SelectImage\SelectImage;
use tiFy\Field\SelectJs\SelectJs;
use tiFy\Field\Submit\Submit;
use tiFy\Field\Text\Text;
use tiFy\Field\Textarea\Textarea;
use tiFy\Field\TextRemaining\TextRemaining;
use tiFy\Field\ToggleSwitch\ToggleSwitch;

/**
 * Class Manager
 * @package tiFy\Field
 *
 * @method static Button Button(string $id = null, array $attrs = [])
 * @method static Checkbox(string $id = null, array $attrs = [])
 * @method static CheckboxCollection(string $id = null, array $attrs = [])
 * @method static Colorpicker(string $id = null, array $attrs = [])
 * @method static Crypted(string $id = null, array $attrs = [])
 * @method static DatetimeJs(string $id = null, array $attrs = [])
 * @method static File(string $id = null, array $attrs = [])
 * @method static Findposts(string $id = null, array $attrs = [])
 * @method static Hidden(string $id = null, array $attrs = [])
 * @method static Label(string $id = null, array $attrs = [])
 * @method static MediaFile(string $id = null, array $attrs = [])
 * @method static MediaImage(string $id = null, array $attrs = [])
 * @method static Number(string $id = null, array $attrs = [])
 * @method static NumberJs(string $id = null, array $attrs = [])
 * @method static Password(string $id = null, array $attrs = [])
 * @method static Radio(string $id = null, array $attrs = [])
 * @method static RadioCollection(string $id = null, array $attrs = [])
 * @method static Repeater(string $id = null, array $attrs = [])
 * @method static Select(string $id = null, array $attrs = [])
 * @method static SelectImage(string $id = null, array $attrs = [])
 * @method static SelectJs(string $id = null, array $attrs = [])
 * @method static Submit(string $id = null, array $attrs = [])
 * @method static Text(string $id = null, array $attrs = [])
 * @method static Textarea(string $id = null, array $attrs = [])
 * @method static TextRemaining(string $id = null, array $attrs = [])
 * @method static ToggleSwitch(string $id = null, array $attrs = [])
 */
final class Manager implements ManagerInterface
{
    /**
     * Liste des instances des éléments déclarés.
     * @var array
     */
    protected static $indexes = [];

    /**
     * Liste des alias de qualification des éléments.
     * @var array
     */
    protected $items = [
        'button'              => Button::class,
        'checkbox'            => Checkbox::class,
        'checkbox-collection' => CheckboxCollection::class,
        'colorpicker'         => Colorpicker::class,
        'crypted'             => Crypted::class,
        'datetime-js'         => DatetimeJs::class,
        'file'                => File::class,
        'findposts'           => Findposts::class,
        'hidden'              => Hidden::class,
        'label'               => Label::class,
        'media-file'          => MediaFile::class,
        'media-image'         => MediaImage::class,
        'number'              => Number::class,
        'number-js'           => NumberJs::class,
        'password'            => Password::class,
        'radio'               => Radio::class,
        'radio-collection'    => RadioCollection::class,
        'repeater'            => Repeater::class,
        'select'              => Select::class,
        'select-js'           => SelectJs::class,
        'submit'              => Submit::class,
        'text'                => Text::class,
        'textarea'            => Textarea::class,
        'text-remaining'      => TextRemaining::class,
        'toggle-switch'       => ToggleSwitch::class,
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'after_setup_theme',
            function () {
                foreach ($this->items as $alias => $concrete) :
                    app()->bind("field.{$alias}", $concrete)->build([null, []]);
                endforeach;
            },
            999999
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function __callStatic($name, $args)
    {
        array_unshift($args, $name);

        return call_user_func_array([app('field'), 'get'], $args);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $id = null, $attrs = null)
    {
        $alias = 'field.' . Str::kebab($name);

        if (is_array($id)) :
            $attrs = $id;
            $id = null;
        else :
            $attrs = $attrs ? : [];
        endif;

        return app()->resolve($alias, [$id, $attrs]);
    }

    /**
     * {@inheritdoc}
     */
    public function index(FieldController $field)
    {
        $concrete = class_info($field)->getName();
        $alias = array_search($concrete, $this->items);

        if ($alias === false) :
            return 0;
        endif;

        $count = empty(self::$indexes[$alias]) ? 0 : count(self::$indexes[$alias]);

        self::$indexes[$alias][$field->getId()] = $field;

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function register($name, $concrete)
    {
        if (in_array($concrete, $this->items) || isset($this->items["field.{$name}"])) :
            return false;
        endif;

        $this->items[$name] = $concrete;

        return true;
    }
}