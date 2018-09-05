<?php

namespace tiFy\Partial\Tag;

use tiFy\Partial\AbstractPartialItem;
use tify\Kernel\Tools;

class Tag extends AbstractPartialItem
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
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (in_array($this->get('tag'), $this->singleton)) :
            $this->set('singleton', true);
        endif;
    }
}