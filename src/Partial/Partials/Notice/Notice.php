<?php

namespace tiFy\Partial\Partials\Notice;

use tiFy\Partial\PartialController;

class Notice extends PartialController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     *      @var string|callable $content Texte du message de notification. défaut 'Lorem ipsum dolor site amet'.
     *      @var bool $dismiss Affichage du bouton de masquage de la notification.
     *      @var string $type Type de notification info|warning|success|error. défaut info.
     * }
     */
    protected $attributes = [
        'before'  => '',
        'after'   => '',
        'attrs'   => [],
        'viewer'  => [],
        'content'   => 'Lorem ipsum dolor site amet',
        'dismiss'   => false,
        'type'      => 'info'
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                \wp_register_style(
                    'PartialNotice',
                    assets()->url('partial/notice/css/styles.css'),
                    [],
                    180214
                );

                \wp_register_script(
                    'PartialNotice',
                    assets()->url('partial/notice/js/scripts.js'),
                    ['jquery'],
                    180214,
                    true
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('PartialNotice');
        wp_enqueue_script('PartialNotice');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->attributes['attrs']['id'] = 'tiFyPartial-Notice--' . $this->getIndex();

        parent::parse($attrs);

        if(!$this->get('attrs.class')) :
            $this->set('attrs.class', 'tiFyPartial-Notice');
        endif;

        $this->set(
            'attrs.aria-control',
            'notice'
        );

        $this->set(
            'attrs.aria-type',
            $this->get('type')
        );

        $content = $this->get('content', '');
        $this->set('content', $this->isCallable($content) ? call_user_func($content) : $content);

        if($dismiss = $this->get('dismiss')) :
            if (!is_array($dismiss)) :
                $dismiss= [];
            endif;

            $this->set(
                'dismiss',
                partial(
                    'tag',
                    array_merge(
                        [
                            'tag' => 'button',
                            'attrs' => [
                                'aria-toggle' => 'dismiss'
                            ],
                            'content' => '&times;'
                        ],
                        $dismiss
                    )
                )
            );
        else :
            $this->set('dismiss', '');
        endif;
    }
}