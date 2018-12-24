<?php

namespace tiFy\Partial\Partials\Tag;

use tiFy\Partial\PartialController;

class Tag extends PartialController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant le controleur d'affichage.
     *      @var string $after Contenu placé après le controleur d'affichage.
     *      @var string $tag Balise HTML div|span|a|... défaut div.
     *      @var array $attrs Liste des attributs de balise HTML.
     *      @var string|callable $content Contenu de la balise HTML.
     *      @var bool $singleton
     * }
     */
    protected $attributes = [
        'tag'       => 'div',
        'attrs'     => [],
        'content'   => '',
        'singleton' => false
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