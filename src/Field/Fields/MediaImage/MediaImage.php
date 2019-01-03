<?php

namespace tiFy\Field\Fields\MediaImage;

use tiFy\Contracts\Field\MediaImage as MediaImageContract;
use tiFy\Field\FieldController;

class MediaImage extends FieldController implements MediaImageContract
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
        'before'               => '',
        'after'                => '',
        'name'                 => '',
        'value'                => '',
        'attrs'                => [],
        'viewer'               => [],
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
        'removable'            => true,
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
                    'FieldMediaImage',
                    assets()->url('field/media-image/css/styles.css'),
                    ['tiFyAdmin'],
                    180516
                );
                wp_register_script(
                    'FieldMediaImage',
                    assets()->url('field/media-image/js/scripts.js'),
                    ['jquery'],
                    180516,
                    true
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'media_library_title'  => __('Personnalisation de l\'image', 'tify'),
            'media_library_button' => __('Utiliser cette image', 'tify'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('FieldMediaImage');
        wp_enqueue_script('FieldMediaImage');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set(
            'attrs.style',
            "background-color:" . $this->get('default_color') . ";" .
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
                trim($this->get('attrs.class') . ' tiFyField-MediaImage--selected')
            );
        endif;
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