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
use tiFy\Contracts\Field\FieldItemInterface;
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
 * Class Field
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
final class Field
{
    /**
     * Récupération statique du champ.
     *
     * @param string $name Nom de qualification du controleur d'affichage.
     * @param array $args Liste des variables passées en arguments.
     *
     * @return null|callable
     */
    public static function __callStatic($name, $args)
    {
        array_unshift($args, $name);

        return call_user_func_array([app(Field::class), 'get'], $args);
    }

    /**
     * Récupération de l'instance d'un champ déclaré.
     *
     * @param string $name Nom de qualification de l'élément.
     * @param mixed $id Nom de qualification ou Liste des attributs de configuration.
     * @param mixed $attrs Liste des attributs de configuration.
     *
     * @return FieldItemInterface
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
}