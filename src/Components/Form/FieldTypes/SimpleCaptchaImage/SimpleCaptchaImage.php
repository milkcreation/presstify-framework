<?php

namespace tiFy\Components\Form\FieldTypes\SimpleCaptchaImage;

use Mexitek\PHPColors\Color;
use tiFy\Form\Fields\AbstractFieldTypeController;

class SimpleCaptchaImage extends AbstractFieldTypeController
{
    /**
     * Liste des propriétés supportées.
     * @var array
     */
    protected $supports = [
        'integrity',
        'label',
        'request',
        'wrapper',
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        // Options par défaut
        $this->attributes = [
            // Chemins vers l'image relatif ou absolue
            'imagepath' => $this->appDirname() . '/texture.jpg',
            // Couleur du texte (hexadecimal ou array rgb)
            'textcolor' => '#CCC',
        ];

        // Définition des fonctions de callback
        $this->callbacks = [
            'field_init_params'   => [$this, 'cb_field_init_params'],
            'handle_check_field' => [$this, 'cb_handle_check_field'],
        ];

        add_action('tify_form_loaded', [$this, 'tify_form_loaded']);
    }

    /**
     * Après le chargement complet des formulaires.
     *
     * @return void
     */
    public function tify_form_loaded()
    {
        if (!isset($_REQUEST[$this->ID])) :
            return;
        endif;

        list($form_id, $field_slug) = explode('::', $_REQUEST[$this->ID]);

        if (($form_id != $this->getForm()->getName()) || ($field_slug != $this->field()->getSlug())) :
            return;
        endif;

        $this->createImage();
    }

    /**
     * Court-circuitage de la définition des paramètres du champ.
     *
     * @return void
     */
    public function cb_field_init_params($field)
    {
        if ($field->get('type') !== 'simple-captcha-image') :
            return;
        endif;

        $field->set('required', true);
    }

    /**
     * Court-circuitage de contrôle d'intégrité des champs.
     *
     * @return void
     */
    public function cb_handle_check_field(&$errors, $field)
    {
        if ($field->get('type') !== 'simple-captcha-image') :
            return;
        endif;

        if (!session_id()) :
            session_start();
        endif;

        if (!isset($_SESSION['security_number'])) :
            $errors[] = __('ERREUR SYSTÈME : Impossible de définir le code de sécurité');
        elseif ((int)$field->getValue() !== $_SESSION['security_number']) :
            $errors[] = __('La valeur du champs de sécurité doit être identique à celle de l\'image', 'tify');
        endif;
    }

    /**
     * Affichage
     *
     * @return string
     */
    public function render()
    {
        $output = "";

        // Affichage du champ de saisie
        $output .= "<img 
            src=\"" .
            esc_url(
                add_query_arg(
                    [
                        $this->ID => $this->getForm()->getName() . '::' . $this->field()->getSlug(),
                    ],
                    site_url()
                )
            ) . "\" " .
            "alt=\"" . __('captcha introuvable', 'tify') . "\" " .
            "style=\"vertical-align: middle;\"" .
            "/>";
        $output .= "<input type=\"text\"";
        /// ID HTML
        $output .= " id=\"" . $this->getInputID() . "\"";
        /// Classe HTML
        $output .= " class=\"" . join(' ', $this->getInputClasses()) . "\"";
        /// Name
        $output .= " name=\"" . esc_attr($this->field()->getName()) . "\"";

        /// Attributs
        $output .= $this->getInputHtmlAttrs();
        $output .= " autocomplete=\"off\"";
        $output .= " style=\"height:50px;vertical-align: middle;\"";
        /// Value
        $output .= " value=\"\"";
        /// TabIndex
        $output .= " " . $this->getTabIndex();
        $output .= " />";

        return $output;
    }

    /**
     * Création dynamique de l'image.
     *
     * @return string
     */
    private function createImage()
    {
        if (!session_id()) :
            session_start();
        endif;

        $_SESSION['security_number'] = rand(10000, 99999);

        function imageCreateFromAny($filepath)
        {
            $type = exif_imagetype($filepath);
            $allowedTypes = [
                1,  // [] gif
                2,  // [] jpg
                3,  // [] png
                6   // [] bmp
            ];
            if (!in_array($type, $allowedTypes)) {
                return false;
            }

            switch ($type) :
                case 1 :
                    $im = imageCreateFromGif($filepath);
                    break;
                case 2 :
                    $im = imageCreateFromJpeg($filepath);
                    break;
                case 3 :
                    $im = imageCreateFromPng($filepath);
                    break;
                case 6 :
                    $im = imageCreateFromBmp($filepath);
                    break;
            endswitch;

            return $im;
        }

        \nocache_headers();

        if (ob_get_length()) :
            ob_end_clean();
        endif;

        ob_start();

        // Configuration
        $src = $this->getOption('imagepath');
        $txt_color = $this->getOption('textcolor');

        if (is_array($txt_color) && (count($txt_color) === 3)) :
        elseif (preg_match('/^#([a-f0-9]{3}){1,2}$/i', $txt_color)) :
            $color = new Color($txt_color);
            $txt_color = array_values($color->getRgb());
        else :
            $txt_color = [180, 180, 180];
        endif;

        // Traitement
        $img = imageCreateFromAny($src);
        $image_text = empty($_SESSION['security_number']) ? 'error' : $_SESSION['security_number'];
        $text_color = imagecolorallocate($img, $txt_color[0], $txt_color[1], $txt_color[2]);

        // Alternative imagettftext (Serveur MacOSX)
        if (function_exists('imagettftext')) :
            $text = imagettftext($img, 16, rand(-10, 10), rand(10, 30), rand(25, 35), $text_color,
                self::tFyAppDirname() . '/fonts/courbd.ttf', $image_text);
        else :
            $font = imageloadfont("./fonts/DaveThin_8x16_BE.gdf");
            $text = imagestring($img, $font, rand(10, 30), rand(25, 35), $image_text, $text_color);
        endif;

        header("Content-type:image/jpeg");
        header("Content-Disposition:inline ; filename=" . basename($src . $image_text));
        imagejpeg($img);
        imagedestroy($img);
        exit;
    }
}