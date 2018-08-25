<?php

namespace tiFy\Components\Partial\ModalTrigger;

use tiFy\Partial\AbstractPartialItem;
use tiFy\Partial\Partial;
use tiFy\Kernel\Tools;

class ModalTrigger extends AbstractPartialItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *
     *      @var string $tag Balise HTML.
     *      @var array $attrs Liste des attributs HTML.
     *      @var string|callable $content Texte
     *      @var bool|string|array $modal {
     *          Liste des attributs de configuration de la fenêtre de dialogue
     *          @see \tiFy\Components\Partial\Modal\Modal
     *      }
     * }
     */
    protected $attributes = [
        'tag'       => 'a',
        'attrs' => [
            'class' => ''
        ],
        'content'     => '',
        'modal'       => true
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
        $this->attributes['content'] = __('Lancer', 'tify');

        parent::parse($attrs);

        if(!$this->get('attrs.id')) :
            $this->set('attrs.id', 'tiFyPartial-ModalTrigger--' . $this->getIndex());
        endif;

        if($this->has('attrs.class')) :
            $this->set('attrs.class', "tiFyPartial-ModalTrigger {$this->get('attrs.class')}");
        else :
            $this->set('attrs.class', 'tiFyPartial-ModalTrigger');
        endif;

        if (($this->get('tag') === 'a') && !$this->has('attrs.href')) :
            $this->set('attrs.href', "#{$this->get('attrs.id')}");
        endif;

        $this->set('attrs.aria-control', 'modal_trigger');

        if($modal = $this->get('modal')) :
            if (is_string($modal)) :
                $this->set('modal', '');
                $this->set('attrs.data-target', "#$modal");
            else :
                if(!is_array($modal)) :
                    $modal = [];
                endif;
                $modal = array_merge(
                    [
                        'options' => ['show' => false]
                    ],
                    $modal
                );
                $partialModal = Partial::Modal($modal);
                $this->set('modal', $partialModal);
                $this->set('attrs.data-target', "#{$partialModal->get('container.attrs.id')}");
            endif;
        endif;
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function display()
    {
        return (string)Partial::Tag(
                [
                    'tag'     => $this->get('tag'),
                    'attrs'   => $this->get('attrs'),
                    'content' => $this->get('content'),
                ]
            ) . $this->get('modal');
    }
}