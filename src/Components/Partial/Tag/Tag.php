<?php

namespace tiFy\Components\Partial\Tag;

use tiFy\Partial\AbstractPartialController;
use tify\Kernel\Tools;

class Tag extends AbstractPartialController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $tag Balise HTML div|span|a|... défaut div.
     *      @var array $attrs Liste des attributs de balise HTML.
     *      @var string|callable $content Contenu de la balise HTML.
     * }
     */
    protected $attributes = [
        'tag'     => 'div',
        'attrs'   => [],
        'content' => 'lorem ipsum dolor sit amet'
    ];

    /**
     * Liste des attributs de la balise Html.
     * @var array
     */
    private $tagAttrs = [];

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
    }

    /**
     * Affichage.
     *
     * @return string
     */
    protected function display()
    {
        $id = $this->getId();
        $index = $this->getIndex();
        $tag = $this->get('tag', 'div');
        $tag_attrs = $this->get('attrs', []);
        $_tag_attrs = Tools::Html()->parseAttrs($tag_attrs);
        $content = $this->get('content');
        $content = is_callable($content) ? call_user_func($content) : $content;

        return $this->appTemplateRender('tag', compact('id', 'index', 'tag', 'tag_attrs', '_tag_attrs', 'content'));
    }
}