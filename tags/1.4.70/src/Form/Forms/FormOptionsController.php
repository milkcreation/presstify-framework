<?php

namespace tiFy\Form\Forms;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use tiFy\Form\AbstractCommonDependency;
use tiFy\Form\Forms\FormItemController;
use tiFy\Partial\Partial;

class FormOptionsController extends AbstractCommonDependency
{
    /**
     * Liste des attributs de configuration.
     * @var array {
     *      @var string|bool $anchor Ancre de défilement verticale de la page web à la soumission du formulaire.
     *      @var string|callable $success_cb Méthode de rappel à l'issue d'un formulaire soumis avec succès. 'form' affichera un nouveau formulaire.
     * }
     */
    protected $attributes = [
        'anchor'         => true,
        'success_cb'    => '',
        //'paged'            => 0,
        //'preview'        => false,
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param FormItemController $Form Classe de rappel du controleur de formulaire associé.
     * @param array $options Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct(FormItemController $form, $attrs = [])
    {            
        parent::__construct($form);

        $this->parse($attrs);
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $options Liste des attributs de configuration.
     *
     * @return array
     */
    public function parse($attrs = [])
    {
        $this->attributes = $this->recursiveParseArgs($attrs, $this->attributes);

        if (($anchor = $this->get('anchor', '')) && ($anchor === true)) :
            $this->set('anchor', $this->getForm()->get('container_id'));
        endif;
    }

    /**
     * Récupération de la liste des attributs de configuration.
     *
     * @return array
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut à récupérer. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut à définir. Syntaxe à point permise.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return mixed
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);
    }
}