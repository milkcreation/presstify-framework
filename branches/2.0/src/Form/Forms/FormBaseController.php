<?php

namespace tiFy\Form\Forms;

use tiFy\App\AppController;
use tiFy\Contracts\Form\FormItem;
use tiFy\Form\Buttons\ButtonControllerInterface;
use tiFy\Form\Fields\FieldItemController;
use tiFy\Form\Forms\FormCallbacksController;
use tiFy\Form\Forms\FormItemController;

class FormBaseController extends AppController implements FormItem
{
    /**
     * Classe de rappel du formulaire.
     * @var FormItemController
     */
    protected $form;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du formulaire.
     * @param array $attrs Attribut de configuration du formulaire.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->boot();

        // Initialisation des attributs de formulaire
        $attrs = $this->_initAttrs($attrs);

        // Instanciation du formulaire associé
        $this->form = new FormItemController($name, $attrs, $this);

        // Déclenchement des événements
        add_action(
            'tify_form_loaded',
            function () {
                form()->current($this);
                $this->getForm()->handle()->proceed();
                form()->reset();
            }
        );
    }

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->display();
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Initialisation des attributs de configuration du formulaire.
     *
     * @param array Liste des attributs de configuration.
     *
     * @return array
     */
    private function _initAttrs($attrs)
    {
        /**
         * @var array $addons
         * @var array $buttons
         * @var array $fields
         * @var array $notices
         * @var array $options
         */
        $pieces = ['addons', 'buttons', 'fields', 'notices', 'options'];

        foreach ($pieces as $piece) :
            if (!empty($attrs[$piece])) :
                ${$piece} = $attrs[$piece];
            else :
                ${$piece} = [];
            endif;
            unset($attrs[$piece]);
        endforeach;

        // Globaux
        if ($matches = preg_grep('/^set_form_(.*)/', get_class_methods($this))) :
            foreach ($matches as $method) :
                $attr = preg_replace('/^set_form_/', '', $method);
                if (in_array($attr, $pieces)) {
                    continue;
                }

                $args = isset($attrs[$attr]) ? $attrs[$attr] : null;
                $attrs[$attr] = call_user_func([$this, $method], $args);
            endforeach;
        endif;

        // Addons
        $addons = $this->_initAddons($addons);

        // Boutons
        $buttons = $this->_initButtons($buttons);

        // Champs
        $fields = $this->_initFields($fields);

        // Notices @todo

        // Options @todo

        $attrs += compact($pieces);

        return $attrs;
    }

    /**
     * Initialisation des surchages de déclaration d'addons.
     * @internal La méthode de surcharge de la declaration set_addon_{addon_id} doit exister.
     *
     * @param array $items Liste des addons déclarés dans les attributs de configuration.
     *
     * @return array
     */
    private function _initAddons($items = [])
    {
        $slugs = (!empty($items)) ? \array_flip(\array_column($items, 'slug')) : [];

        if ($matches = preg_grep('/^set_addon_(.*)/', get_class_methods($this))) :
            foreach ($matches as $method) :
                $slug = preg_replace('/^set_addon_/', '', $method);

                if (isset($slugs[$slug])) :
                    $k = $slugs[$slug];
                    $attrs = $items[$k];
                else :
                    $k = count($items);
                    $attrs = [];
                endif;

                $items[$slug] = call_user_func([$this, $method], $attrs);
            endforeach;
        endif;

        return $items;
    }

    /**
     * Initialisation des surchages de déclaration de boutons.
     * @internal La méthode de surcharge de la declaration set_button_{button_id} doit exister.
     *
     * @return array
     */
    private function _initButtons($items = [])
    {
        $slugs = (!empty($items)) ? array_flip(array_column($items, 'slug')) : [];

        if ($matches = preg_grep('/^set_button_(.*)/', get_class_methods($this))) :
            foreach ($matches as $method) :
                $slug = preg_replace('/^set_button_/', '', $method);

                if (isset($slugs[$slug])) :
                    $k = $slugs[$slug];
                    $attrs = $items[$k];
                else :
                    $k = count($items);
                    $attrs = [];
                endif;

                $items[$slug] = call_user_func([$this, $method], $attrs);
            endforeach;
        endif;

        return $items;
    }

    /**
     * Initialisation des surchages de déclaration des champs du formulaire.
     * @internal La méthode de surcharge de la declaration set_field_{field_slug} doit exister.
     *
     * @return array
     */
    private function _initFields($items = [])
    {
        $slugs = (!empty($items)) ? array_flip(array_column($items, 'slug')) : [];

        if ($matches = preg_grep('/^set_field_(.*)/', get_class_methods($this))) :
            foreach ($matches as $method) :
                $slug = preg_replace('/^set_field_/', '', $method);

                if (isset($slugs[$slug])) :
                    $k = $slugs[$slug];
                    $attrs = $items[$k];
                else :
                    $k = count($items);
                    $attrs = [];
                endif;

                $items[$k] = wp_parse_args(['slug' => $slug], call_user_func([$this, $method], $attrs));
            endforeach;
        endif;

        return $items;
    }

    /**
     * Récupération de la classe de rappel du formulaire.
     *
     * @return FormItemController
     */
    final public function getForm()
    {
        return $this->form;
    }

    /**
     * Récupération d'un champ.
     *
     * @param string $field_slug Identifiant de qualification du champ.
     *
     * @return FieldItemController
     */
    final public function getField($field_slug)
    {
        return $this->getForm()->getField($field_slug);
    }

    /**
     * Traitement des variables de requête au moment de la soumission du formulaire.
     * @internal La méthode de surcharge de la classe parse_query_var_{field_slug} doit exister.
     *
     * @param string $field_slug Identifiant de qualification du champ.
     * @param mixed $value Valeur du champ.
     *
     * @return void
     */
    final public function parseQueryVar($field_slug, $value)
    {
        if (method_exists($this, 'parse_query_var_' . $field_slug)) :
            return call_user_func([$this, 'parse_query_var_' . $field_slug], $value);
        endif;

        return call_user_func([$this, 'parse_query_vars'], $field_slug, $value);
    }

    /**
     * Vérification d'intégrité des variables de requête au moment de la soumission du formulaire.
     *
     * @param FieldItemController $field Classe de rappel du champ à vérifier.
     * @param mixed $errors Liste des erreurs existantes.
     *
     * @return void
     */
    final public function checkQueryVar($field, &$errors)
    {
        if (method_exists($this, 'check_query_var_' . $field->getSlug())) :
            return call_user_func_array([$this, 'check_query_var_' . $field->getSlug()], [$field, &$errors]);
        endif;

        return call_user_func_array([$this, 'check_query_vars'], [$field, &$errors]);
    }

    /**
     * Execution de fonction de court-cicuitage.
     * @internal La méthode de surcharge de la classe call_{callback} doit exister.
     * @see \
     *
     * @param string $callback Identifiant de qualification de la méthode de rappel. ex. handle_successfully.
     *
     * @return callable
     */
    final public function call($hookname, $args = [])
    {
        if (method_exists($this, "call_{$hookname}")) :
            return call_user_func_array([$this, 'call_' . $hookname], $args);
        endif;

        return \__return_null();
    }

    /**
     * Traitement par défaut des variables de requête lors de la soumission.
     *
     * @param string $field_slug Identifiant de qualification du champ.
     * @param array $value Valeur de retour.
     *
     * @return mixed
     */
    public function parse_query_vars($field_slug, $value)
    {
        return $value;
    }

    /**
     * Vérification par défaut de l'intégrité des variables de requêtes.
     *
     * @param FieldItemController $field Classe de rappel du controleur de champ.
     * @param array Liste des erreurs existantes.
     *
     * @return void
     */
    public function check_query_vars($field, &$errors)
    {
        return;
    }

    /**
     * Affichage du formulaire.
     *
     * @param bool $echo Activation de l'affichage ou retour.
     *
     * @return string
     */
    public function display($echo = false)
    {
        $output = $this->getForm()->display();

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
}