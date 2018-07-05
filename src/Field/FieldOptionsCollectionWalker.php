<?php

namespace tiFy\Field;

use tiFy\Components\Tools\Walkers\WalkerBaseController;
use tiFy\Field\WalkerOptionsItem;
use tiFy\Kernel\Tools;

class FieldOptionsCollectionWalker extends WalkerBaseController
{
    /**
     * Liste des options.
     * @var array {
     *      @var string $indent Caractère d'indendation.
     *      @var int $start_indent Nombre de caractère d'indendation au départ.
     *      @var bool|string $sort Ordonnancement des éléments.false|true|append(défaut)|prepend.
     *      @var string $prefixe Préfixe de nommage des éléments HTML.
     *      @var string $item_controller Controleur de traitement d'un élément.
     * }
     */
    protected $options = [
        'indent'          => "\t",
        'start_indent'    => 0,
        'sort'            => 'append',
        'prefix'          => 'tiFyFieldOption-',
        'item_controller' => FieldOptionsItemWalker::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function openItems($item)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function closeItems($item)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function openItem($item)
    {
        if ($item->get('group')) :
            return $this->getIndent($item->getDepth()) . "<optgroup ". $item->getHtmlAttrs() . ">\n";
        else :
            return $this->getIndent($item->getDepth()) . "<option ". $item->getHtmlAttrs() . ">\n";
        endif;

    }

    /**
     * {@inheritdoc}
     */
    public function closeItem($item)
    {
        if ($item->get('group')) :
            return $this->getIndent($item->getDepth()) . "</optgroup>\n";
        else :
            return $this->getIndent($item->getDepth()) . "</option>\n";
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function contentItem($item)
    {
        return ! empty($item->get('content')) ? esc_attr($item->get('content')) : '';
    }
}