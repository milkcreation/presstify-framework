<?php
/**
 * @name SlickCarousel
 * @desc Controleur d'affichage de diaporama basé sur Slick
 * @see http://kenwheeler.github.io/slick/
 * @package presstiFy
 * @namespace tiFy\Control\SlickCarousel
 * @version 1.1
 * @subpackage Core
 * @since 1.2.502
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Control\SlickCarousel;

/**
 * @Overrideable \App\Core\Control\SlickCarousel\SlickCarousel
 *
 * <?php
 * namespace \App\Core\Control\SlickCarousel
 *
 * class SlickCarousel extends \tiFy\Control\SlickCarousel\SlickCarousel
 * {
 *
 * }
 */

class SlickCarousel extends \tiFy\Control\Factory
{
    /**
     * DECLENCHEURS
     */
    /**
     * Initialisation globale
     *
     * @return void
     */
    protected function init()
    {
        \wp_register_style(
            'tify_control-slick_carousel',
            $this->appAbsUrl() . '/assets/SlickCarousel/css/styles.css',
            ['slick', 'slick-theme'],
            170722
        );
        \wp_register_script(
            'tify_control-slick_carousel',
            $this->appAbsUrl() . '/assets/SlickCarousel/js/scripts.js',
            ['slick'],
            170722,
            true
        );
    }

    /**
     * Identifiant de la classe
     * @var string
     */
    protected function enqueue_scripts()
    {
        \wp_enqueue_style('tify_control-slick_carousel');
        \wp_enqueue_script('tify_control-slick_carousel');
    }

    /**
     * CONTROLEURS
     */
    /**
     * Affichage
     *
     * @param array $attrs Liste des attributs de configuration
     *
     * @return string
     */
    protected function display($attrs = [])
    {
        // Traitement des attributs de configuration
        $defaults = [
            // Marqueur d'identification unique
            'id'              => 'tiFyControl-slick_carousel-' . $this->getId(),
            // Id Html du conteneur
            'container_id'    => 'tiFyControlSlickCarousel--' . $this->getId(),
            // Classe Html du conteneur
            'container_class' => '',
            // Options
            // @see http://kenwheeler.github.io/slick/#settings
            'options'         => [],
            // Liste des slides
            'nodes'           => [
                [
                    'content' => '<img src="https://fr.facebookbrand.com/wp-content/uploads/2016/05/FB-fLogo-Blue-broadcast-2.png" />'
                ],
                [
                    'content' => '<img src="https://fr.facebookbrand.com/wp-content/uploads/2016/05/YES-ThumbFinal_4.9.15-2.png" />'
                ]
            ],
        ];
        $attrs = wp_parse_args($attrs, $defaults);
        extract($attrs);

        $Nodes = self::tFyAppLoadOverride('tiFy\Control\SlickCarousel\Nodes');
        $nodes = $Nodes->customs($nodes);

        $output = "";
        $output = "<div id=\"{$container_id}\" class=\"tiFyControlSlickCarousel" . ($container_class ? ' ' . $container_class : '') . "\" data-tify_control=\"slick_carousel\" data-slick=\"" . htmlentities(json_encode($options)) . "\">\n";
        $Walker = self::tFyAppLoadOverride('tiFy\Control\SlickCarousel\Walker');
        $output .= $Walker->output($nodes);
        $output .= "</div>\n";

        echo $output;
    }
}