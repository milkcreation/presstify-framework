<?php

namespace tiFy\Components\Partial\Modal;

use tiFy\Partial\AbstractPartialController;
use tiFy\Kernel\Tools;

class Modal extends AbstractPartialController
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
        'container'    => [
            'attrs' => [
                'class' => ''
            ]
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
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            'tiFyPartialModal',
            $this->appAsset('/Partial/Modal/css/styles.css'),
            [],
            171206
        );
        \wp_register_script(
            'tiFyPartialModal',
            $this->appAsset('/Partial/Modal/js/scripts.js'),
            ['jquery'],
            171206,
            true
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('tiFyPartialModal');
        \wp_enqueue_script('tiFyPartialModal');
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    protected function parse($attrs = [])
    {
        parent::parse($attrs);

        if(!$this->get('container.attrs.id')) :
            $this->set('container.attrs.id', 'tiFyPartial-Modal--' . $this->getIndex());
        endif;

        $class = 'tiFyPartial-Modal modal';
        if($this->get('animation')) :
            $class .= ' fade';
        endif;

        if($this->has('container.attrs.class')) :
            $this->set('container.attrs.class', "{$class} {$this->get('container.attrs.class')}");
        else :
            $this->set('container.attrs.class', $class);
        endif;

        $this->set('container.attrs.data-modal', $this->get('container_id'));
        $this->set('container.attrs.role', 'dialog');
        $this->set('container.attrs.aria-control', 'modal');

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
                $this->set("container.attrs.data-{$key}", $this->get("options.{$key}") ? 'true' : 'false');
            endif;
        endforeach;
    }

    /**
     * Affichage.
     *
     * @return string
     */
    protected function display()
    {
        if($backdrop_close = $this->get('backdrop_close')) :
            $backdrop_close = is_callable($backdrop_close)
                ? call_user_func($this->get('backdrop_close'), $this->all())
                : (is_string($backdrop_close)
                    ? $backdrop_close
                    : $this->appTemplateRender('backdrop_close'));
        endif;

        if($body = $this->get('body')) :
            $body = is_callable($body)
                ? call_user_func($this->get('body'), $this->all())
                : (is_string($body)
                    ? $body
                    : $this->appTemplateRender('body'));
        endif;

        if($footer = $this->get('footer')) :
            $footer = is_callable($footer)
                ? call_user_func($this->get('footer'), $this->all())
                : (is_string($footer)
                    ? $footer
                    : $this->appTemplateRender('footer'));
        endif;

        if($header = $this->get('header')) :
            $header = is_callable($header)
                ? call_user_func($this->get('header'), $this->all())
                : (is_string($header)
                    ? $header
                    : $this->appTemplateRender('header'));
        endif;

        $size = in_array($this->get('size'), ['lg', 'sm', 'full']) ? 'modal-' . $this->get('size') : '';

        $output  = "";
        $output .= "<div " . Tools::Html()->parseAttrs($this->get('container.attrs')) . ">";
        $output .= $this->appTemplateRender('dialog', compact('backdrop_close', 'body', 'footer', 'header', 'size'));
        $output .= "</div>";

        // Fenêtre de dialogue
        if ($this->get('in_footer')) :
            $footer = function () use ($output) { echo $output; };
            $this->appAddAction((!is_admin() ? 'wp_footer' : 'admin_footer'), $footer);
            return '';
        else :
            return $output;
        endif;
    }
}