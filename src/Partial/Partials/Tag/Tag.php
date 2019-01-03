<?php

namespace tiFy\Partial\Partials\Tag;

use tiFy\Contracts\Partial\Tag as TagContract;
use tiFy\Partial\PartialController;

class Tag extends PartialController implements TagContract
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     *      @var string $tag Balise HTML div|span|a|... défaut div.
     *      @var string|callable $content Contenu de la balise HTML.
     *      @var boolean $singleton Activation de balise de type singleton. ex <{tag}/>. Usage avancé, cet attributon
     *                              se fait automatiquement pour les balises connues.
     * }
     */
    protected $attributes = [
        'before'    => '',
        'after'     => '',
        'attrs'     => [],
        'viewer'    => [],
        'tag'       => 'div',
        'content'   => '',
        'singleton' => false,
    ];

    /**
     * Liste des champs connu de type singleton
     * @see http://html-css-js.com/html/tags
     * @var string[]
     */
    protected $singleton = [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source'
    ];

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (in_array($this->get('tag'), $this->singleton)) :
            $this->set('singleton', true);
        endif;
    }
}