<?php
/**
 * @name Tag
 * @desc Affichage de balise Html
 * @package presstiFy
 * @namespace tiFy\Components\Partials\Tag\Tag
 * @version 1.1
 * @subpackage Components
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Partials\Tag;

use tiFy\Partial\AbstractPartialController;

class Tag extends AbstractPartialController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
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

        if ($tagAttrs = $this->get('attrs', [])) :
            foreach ($tagAttrs as $k => $v) :
                if (is_array($v)) :
                    $v = rawurlencode(json_encode($v));
                endif;
                if (is_int($k)) :
                    $this->tagAttrs[]= "{$v}";
                else :
                    $this->tagAttrs[]= "{$k}=\"{$v}\"";
                endif;
            endforeach;
        endif;
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
        $tag_attrs = $this->tagAttrs;
        $_tag_attrs = $tag_attrs ? ' ' . implode(' ', $tag_attrs) : '';
        $content = $this->get('content');
        $content = is_callable($content) ? call_user_func($content) : $content;

        return $this->appTemplateRender('tag', compact('id', 'index', 'tag', 'tag_attrs', '_tag_attrs', 'content'));
    }
}