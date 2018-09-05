<?php

namespace tiFy\Partial\Modal;

use tiFy\Partial\AbstractPartialItem;
use tiFy\Kernel\Tools;

class Modal extends AbstractPartialItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *
     *      @var array $container Attributs HTML du conteneur.
     *      @var array $options {
     *              Liste des options d'affichage.
     *      }
     *      @var bool $animation Activation de l'animation.
     *      @var string $size Taille d'affichage de la fenêtre de dialogue lg|sm|full.
     *      @var bool|string|callable $backdrop_close_button Affichage d'un bouton fermeture externe. Chaine de caractère à afficher ou booléen pour activer désactiver ou fonction/méthode d'affichage.
     *      @var bool|string|callable $header Affichage de l'entête de la fenêtre. Chaine de caractère à afficher ou booléen pour activer désactiver ou fonction/méthode d'affichage.
     *      @var bool|string|callable $body Affichage du corps de la fenêtre. Chaine de caractère à afficher ou booléen pour activer désactiver ou fonction/méthode d'affichage.
     *      @var bool|string|callable $footer Affichage d'un bouton fermeture externe. Chaine de caractère à afficher ou booléen pour activer désactiver ou fonction/méthode d'affichage.
     *      @var bool $in_footer Ajout automatique de la fenêtre de dialogue dans le pied de page du site.
     * }
     */
    protected $attributes = [
        'attrs' => [
            'class' => ''
        ],

        'options' => [],

        'animation' => true,
        'size'      => '',

        'backdrop_close'        => true,
        'header'                => true,
        'body'                  => true,
        'footer'                => true,

        'in_footer' => true
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        app()->appAddAction(
            'init',
            function () {
                \wp_register_style(
                    'PartialModal',
                    \assets()->url('/partial/modal/css/styles.css'),
                    [],
                    171206
                );
                \wp_register_script(
                    'PartialModal',
                    \assets()->url('/partial/modal/js/scripts.js'),
                    ['jquery'],
                    171206,
                    true
                );
            }
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('PartialModal');
        \wp_enqueue_script('PartialModal');
    }

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

        if(!$this->get('attrs.id')) :
            $this->set('attrs.id', 'tiFyPartial-Modal--' . $this->getIndex());
        endif;

        $class = 'tiFyPartial-Modal modal';
        if($this->get('animation')) :
            $class .= ' fade';
        endif;

        if($this->has('attrs.class')) :
            $this->set('attrs.class', "{$class} {$this->get('attrs.class')}");
        else :
            $this->set('attrs.class', $class);
        endif;

        $this->set('attrs.data-modal', $this->get('container_id'));
        $this->set('attrs.role', 'dialog');
        $this->set('attrs.aria-control', 'modal');

        $this->set(
            'options',
            array_merge(
                [
                    'backdrop' => true,
                    'keyboard' => true,
                    'focus'    => true,
                    'show'     => true
                ],
                $this->get('options')
            )
        );
        foreach (['backdrop', 'keyboard', 'focus', 'show'] as $key) :
            if ($this->has("options.{$key}")) :
                $this->set("attrs.data-{$key}", $this->get("options.{$key}") ? 'true' : 'false');
            endif;
        endforeach;
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function display()
    {
        if($backdrop_close = $this->get('backdrop_close')) :
            $backdrop_close = $this->isCallable($backdrop_close)
                ? call_user_func($this->get('backdrop_close'), $this->all())
                : (
                is_string($backdrop_close)
                    ? $backdrop_close
                    : (string) $this->getView('backdrop_close', $this->all())
                );
        endif;

        if($body = $this->get('body')) :
            $body = $this->isCallable($body)
                ? call_user_func($this->get('body'), $this->all())
                : (
                is_string($body)
                    ? $body
                    : (string) $this->getView('body', $this->all())
                );
            $this->set('body', $body);
        endif;

        if($footer = $this->get('footer')) :
            $footer = $this->isCallable($footer)
                ? call_user_func($this->get('footer'), $this->all())
                : (
                is_string($footer)
                    ? $footer
                    : (string) $this->getView('footer', $this->all())
                );
            $this->set('footer', $footer);
        endif;

        if($header = $this->get('header')) :
            $header = $this->isCallable($header)
                ? call_user_func($this->get('header'), $this->all())
                : (
                is_string($header)
                    ? $header
                    : (string) $this->getView('header', $this->all())
                );
            $this->set('header', $header);
        endif;

        $this->set(
            'size',
            in_array($this->get('size'), ['lg', 'sm', 'full'])
                ? 'modal-' . $this->get('size')
                : ''
        );

        if ($this->get('in_footer')) :
            app()->appAddAction(
                (!is_admin() ? 'wp_footer' : 'admin_footer'),
                function () {
                   echo $this->getView('modal', $this->all());
                }
            );
            return '';
        else :
            return $this->getView('modal', $this->all());
        endif;
    }
}