<?php

namespace tiFy\Partial\ModalTrigger;

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
     *          @see \tiFy\Partial\Modal\Modal
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
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'content' => __('Lancer', 'tify')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

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
                $this->set('attrs.data-target', "#{$partialModal->get('attrs.id')}");
            endif;
        endif;
    }

    /**
     * {@inheritdoc}
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