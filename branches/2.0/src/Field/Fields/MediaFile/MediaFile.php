<?php

namespace tiFy\Field\Fields\MediaFile;

use tiFy\Contracts\Field\MediaFile as MediaFileContract;
use tiFy\Field\FieldController;

class MediaFile extends FieldController implements MediaFileContract
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var string $value Valeur courante de soumission du champ.
     *      @var array $attrs Attributs HTML du champ.
     *      @var array $viewer Liste des attributs de configuration du controleur de gabarit d'affichage.
     *      @var string filetype Type de fichier permis ou MimeType. ex. image|image/png|video|video/mp4|application/pdf
     * }
     */
    protected $attributes = [
        'before'   => '',
        'after'    => '',
        'name'     => '',
        'value'    => '',
        'attrs'    => [],
        'viewer'   => [],
        'filetype' => '',
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                wp_register_style(
                    'FieldMediaFile',
                    asset()->url('field/media-file/css/styles.css'),
                    ['dashicons'],
                    180616
                );
                wp_register_script(
                    'FieldMediaFile',
                    asset()->url('field/media-file/js/scripts.js'),
                    ['jquery'],
                    180616,
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
        wp_enqueue_style('FieldMediaFile');
        wp_enqueue_script('FieldMediaFile');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $media_id = $this->get('value', 0);
        if (!$filename = get_attached_file($media_id)) :
            $media_id = 0;
        endif;

        $this->set('attrs.aria-control', 'media_file');
        $this->set('attrs.data-options.library.type', $this->get('filetype'));
        $this->set('attrs.data-options.editing', true);
        $this->set('attrs.data-options.multiple', false);
        $this->set('attrs.aria-active', $media_id ? 'true': 'false');
        $this->set('selected_infos', $media_id ? get_the_title($media_id) . ' &rarr; ' . basename($filename) : '');
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        if (!is_admin()) :
            return '';
        endif;

        wp_enqueue_media();

        return parent::display();
    }
}
