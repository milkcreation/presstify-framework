<?php

/**
 * @name MediaImage
 * @desc Champ de définition d'une image de la médiathèque Wordpress.
 * @package presstiFy
 * @namespace tiFy\Components\Field\MediaImage
 * @version 1.1
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Field\MediaImage;

use tiFy\Field\AbstractFieldItemController;
use tiFy\Kernel\Tools;

class MediaImage extends AbstractFieldItemController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *
     *      @var string $name Nom du champ d'enregistrement
     *      @var int $value ID de l'attachment.
     *      @var int|string $default ID de l'attachment ou url de l'image initial.
     *      @var string $default_color Valeur Hexadécimal de la couleur de fond. "#F4F4F4" par défaut.
     *      @var int $width Largeur de l'image en pixel. 1920 par défaut.
     *      @var int $height Hauteur de l'image en pixel. 360 par defaut.
     *      @var string $size Taille de l'attachment utilisé pour la prévisualisation de l'image. 'large' par défaut.
     *      @var string $content Contenu HTML d'enrichissement de l'affichage de l'interface de saisie.
     *      @var string $media_library_title ' Titre de la Médiathèque. "Personnalisation de l'image" par défaut.
     *      @var string $media_library_button ' Texte d'ajout de l'image dans la Médiathèque. "Utiliser cette image" par défaut.
     *      @var bool $editable Activation de l'administrabilité de l'image.
     *      @var bool $removable Activation de la suppression de l'image active.
     *  }
     */
    protected $attributes = [
        'name'                 => '',
        'value'                => 0,
        'attrs'                => [],
        'default'              => '',
        'default_color'        => "#F4F4F4",
        'width'                => 1920,
        'height'               => 360,
        'size'                 => 'large',
        'size_info'            => true,
        'content'              => '',
        'media_library_title'  => '',
        'media_library_button' => '',
        'editable'             => true,
        'removable'            => true
    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            'tiFyField-MediaImage',
            $this->appAssetUrl('/Field/MediaImage/css/styles.css'),
            ['tiFyAdmin'],
            180516
        );
        \wp_register_script(
            'tiFyField-MediaImage',
            $this->appAssetUrl('/Field/MediaImage/js/scripts.js'),
            ['jquery'],
            180516,
            true
        );
    }

    /**
     * Mise en file des scripts de Wordpress.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        @wp_enqueue_media();
        \wp_enqueue_style('tiFyField-MediaImage');
        \wp_enqueue_script('tiFyField-MediaImage');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->attributes['media_library_title'] = __('Personnalisation de l\'image', 'tify');
        $this->attributes['media_library_button'] = __('Utiliser cette image', 'tify');

        parent::parse($attrs);

        $this->set(
            'attrs.style',
            "background-color:" . $this->get('default_color') .";"  .
            "max-width:" . $this->get('width') . "px;" .
            "max-height:" . $this->get('height') . "px;"
        );

        if ($size_info = $this->get('size_info')) :
            $this->set(
                'info_txt',
                is_string($size_info)
                    ? $size_info
                    : sprintf(__('%dpx / %dpx', 'tify'), $this->get('width'), $this->get('height'))
            );
        else :
            $this->set('info_txt', '');
        endif;

        $default = $this->get('default');
        if (is_numeric($default) && ($default_image = wp_get_attachment_image_src($default, $this->get('size')))) :
            $this->set('default_img', $default_image[0]);
        else :
            $this->set('default_img', is_string($default) ? $default : '');
        endif;
        $this->set('attrs.data-default', $this->get('default_img'));

        $value = $this->get('value');
        if (is_numeric($value) && ($image = wp_get_attachment_image_src($value, $this->get('size')))) :
            $this->set('value_img', $image[0]);
        else :
            $this->set('value_img', is_string($value) && !empty($value) ? $value : $default);
        endif;


        if ($this->get('value_img')) :
            $this->set(
                'attrs.class',
                $this->get('attrs.class') . ' tiFyField-MediaImage--selected'
            );
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        if (!is_admin()) :
            return;
        endif;

        return $this->appTemplateRender(
            'media-image',
            $this->all()
        );
    }
}