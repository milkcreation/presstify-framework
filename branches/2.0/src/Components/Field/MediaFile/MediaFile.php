<?php

namespace tiFy\Components\Field\MediaFile;

use tiFy\Field\AbstractFieldItemController;
use tiFy\Lib\File;

class MediaFile extends AbstractFieldItemController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var array $attrs Liste des attributs HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var int $value Identifiant de qualification du médias selectionné.
     *      @var string filetype Type de fichier permis ou MimeType. ex. image|image/png|video|video/mp4|application/pdf
     * }
     */
    protected $attributes = [
        'before'   => '',
        'after'    => '',
        'attrs'    => [],
        'name'     => '',
        'value'    => 295,
        'filetype' => ''
    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            'tiFyFieldMediaFile',
            $this->appAssetUrl('/Field/MediaFile/css/styles.css'),
            ['dashicons'],
            180616
        );
        \wp_register_script(
            'tiFyFieldMediaFile',
            $this->appAssetUrl('/Field/MediaFile/js/scripts.js'),
            ['jquery'],
            180616,
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
        \wp_enqueue_media();
        \wp_enqueue_style('tiFyFieldMediaFile');
        \wp_enqueue_script('tiFyFieldMediaFile');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->set('attrs.id', 'tiFyField-MediaFile--' . $this->getId());

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
            return;
        endif;

        return $this->appTemplateRender('media-file', $this->all());
    }
}
