<?php

namespace tiFy\Partial\Partials\Tag;

use tiFy\Contracts\Partial\Tag as TagContract;
use tiFy\Partial\PartialFactory;

class Tag extends PartialFactory implements TagContract
{
    /**
     * Liste des champs connu de type singleton
     * @see http://html-css-js.com/html/tags
     * @var string[]
     */
    protected $singleton = [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source'
    ];

    /**
     * Liste des attributs de configuration.
     *
     * @return array $attributes {
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
    public function defaults()
    {
        return [
            'before'    => '',
            'after'     => '',
            'attrs'     => [],
            'viewer'    => [],
            'tag'       => 'div',
            'content'   => '',
            'singleton' => false,
        ];
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        parent::parse();

        if (in_array($this->get('tag'), $this->singleton)) :
            $this->set('singleton', true);
        endif;
    }
}