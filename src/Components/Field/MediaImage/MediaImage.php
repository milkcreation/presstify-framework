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

use tiFy\Field\AbstractFieldController;
use tiFy\Kernel\Tools;

class MediaImage extends AbstractFieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *
     *      @var string $name Nom du champ d'enregistrement
     *      @var int $value ID de l'attachment.
     *      @var int|string $default ID de l'attachment ou url de l'image initial.
     *      @var string $default_color Valeur Hexadécimal de la couleur de fond. "#F4F4F4" par défaut.
     *      @var int $width Largeur de l'image en pixel. 1920 par défaut
     *      @var int $height Hauteur de l'image en pixel. 360 par defaut
     *      @var string $size Taille de l'attachment utilisé pour la prévisualisation de l'image. 'large' par défaut
     *      @var string $content Contenu HTML d'enrichissement de l'affichage de l'interface de saisie.
     *      @var string $media_library_title ' Titre de la Médiathèque. "Personnalisation de l'image" par défaut.
     *      @var string $media_library_button ' Texte d'ajout de l'image dans la Médiathèque. "Utiliser cette image" par défaut.
     *      @var bool $editable Activation de l'administrabilité de l'image
     *  }
     */
    protected $attributes = [
        'container_id'         => '',
        'container_class'      => '',
        'name'                 => '',
        'value'                => 0,
        'default'              => '',
        'default_color'        => "#F4F4F4",
        'width'                => 1920,
        'height'               => 360,
        'size'                 => 'large',
        'size_info'            => true,
        'content'              => '',
        'media_library_title'  => '',
        'media_library_button' => '',
        'editable'             => true
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
            $this->appAsset('/Field/MediaImage/css/styles.css'),
            [],
            180516
        );
        \wp_register_script(
            'tiFyField-MediaImage',
            $this->appAsset('/Field/MediaImage/js/scripts.js'),
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
        @ wp_enqueue_media();
        \wp_enqueue_style('tiFyField-MediaImage');
        \wp_enqueue_script('tiFyField-MediaImage');
    }

    /**
     * Traitement des attributs de configuration
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    protected function parse($attrs = [])
    {
        $this->attributes['name'] = 'tiFyField-MediaImage-' . $this->getName();

        $this->attributes['container_id'] = 'tiFyField-MediaImage--' . $this->getName();
        $this->attributes['container_class'] = 'tiFyField-MediaImage';
        $this->attributes['media_library_title'] = __('Personnalisation de l\'image', 'tify');
        $this->attributes['media_library_button'] = __('Utiliser cette image', 'tify');

        parent::parse($attrs);

        $this->attributes['container_attrs'] = [
            'id' => $this->get('container_id'),
            'class' => $this->get('container_class'),
            'style' => "background-color:" . $this->get('default_color') .";" .
                "max-width:" . $this->get('width') . "px;" .
                "max-height:" . $this->get('height') . "px;" .
                "padding-top:" . 100*($this->get('height')/$this->get('width')) . "%"
        ];

        if ($size_info = $this->get('size_info')) :
            $this->attributes['info_txt'] = is_string($size_info)
                ? $size_info
                : sprintf(__('%dpx / %dpx', 'tify'), $this->get('width'), $this->get('height')
            );
        else :
            $this->attributes['info_txt'] = '';
        endif;

        $default = $this->get('default');
        if (is_numeric($default) && ($default_image = wp_get_attachment_image_src($default, $this->get('size')))) :
            $this->attributes['default_img'] =  $default_image[0];
        else :
            $this->attributes['default_img'] = is_string($default) ? $default : '';
        endif;

        $value = $this->get('value');
        if (is_numeric($value) && ($image = wp_get_attachment_image_src($value, $this->get('size')))) :
            $this->attributes['value_img'] =  $image[0];
        else :
            $this->attributes['value_img'] = (is_string($value) && !empty($value)) ? $value : $default;
        endif;
    }

    /**
     * Affichage.
     *
     * @return string
     */
    protected function display()
    {
        if (!is_admin()) :
            return;
        endif;



        return $this->appTemplateRender(
            'media-image',
            [
                'index'           => $this->getIndex(),
                'container_attrs' => Tools::Html()->parseAttrs($this->get('container_attrs')),
                'name'            => $this->get('name'),
                'value'           => $this->get('value'),
                'default'         => $this->get('default'),
                'value_img'       => $this->get('value_img'),
                'default_img'     => $this->get('default_img'),
                'editable'        => $this->get('editable'),
                'info_txt'        => $this->get('info_txt'),
                'content'         => $this->get('content')
            ]
        );


        // Calcul du ratio

        /*$this->appAddAction(
            'admin_print_footer_scripts',
            function() use ($id, $ratio) {
                echo "<style type=\"text/css\">#{$id}:before{padding-top:{$ratio}%;}</style>";
            }
        );
        */
    }
}