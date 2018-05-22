<?php

namespace tiFy\Components\Partial\Sidebar;

use tiFy\Kernel\Tools;
use tiFy\Partial\AbstractPartialController;
use tiFy\Components\Partial\Sidebar\Walker;

/**
 *
 * @see http://mango.github.io/slideout/
 * @see http://webdesignledger.com/web-design-2/best-practices-for-hamburger-menus
 *
 * USAGE :
 * -------
 * # ETAPE 1 - MISE EN FILE DES SCRIPTS
 * dependance css : 'tiFySidebar' +  dependance js et css ('tiFySidebar')
 *
 * # ETAPE 2 - AFFICHAGE :
 * ## AUTOLOAD -> false
 * <?php tify_sidebar_display();?>
 *
 * RESSOURCES POUR EVOLUTION :
 * http://tympanus.net/Blueprints/SlidePushMenus/
 * http://tympanus.net/Development/OffCanvasMenuEffects/
 * http://tympanus.net/Development/MultiLevelPushMenu/
 */

class Sidebar extends AbstractPartialController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $pos Position de l'interface left (default)|right.
     *      @var string $initial Etat initial de l'interface closed (default)|opened.
     *      @var string|int $width Largeur de l'interface en px ou en %. Si l'unité de valeur n'est pas renseignée l'unité par défault est le px.
     *      @var string|int $min-width Largeur de la fenêtre du navigateur en px ou %, à partir de laquelle l'interface est active. Si l'unité de valeur n'est pas renseignée l'unité par défault est le px.
     *      @var int $z-index Profondeur de champs.
     *      @var bool $animated Activation de l'animation à l'ouverture et la fermeture.
     *      @var bool|string $toggle Activation et contenu de bouton de bascule. Si la valeur booléene active ou desactive le bouton; la valeur chaîne de caractère active et affiche la chaîne ex : <span>X</span>.
     *      @var array $nodes {
     *          Liste des greffons (node) Elements de menu.
     *
     *          @var string $id Identifiant du greffon.
     *          @var string $class Classe HTML du greffon.
     *          @var string $content Contenu du greffon.
     *          @var int $position Position du greffon.
     *          @todo \tiFy\Lib\Nodes\Base ne gère pas encore la position.
     *      }
     * }
     */
    protected $attributes = [
        'width'           => '300px',
        'z-index'         => 99990,
        'attrs'           => [],
        'pos'             => 'left',
        'closed'          => true,
        'animated'        => true,
        'toggle'          => true,
        'min-width'       => '991px',
        'nodes'           => [],
        'theme'           => 'dark'
    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            'tiFyPartialSidebar',
            $this->appAsset('/Partial/Sidebar/css/styles.css'),
            [],
            180511
        );
        \wp_register_script(
            'tiFyPartialSidebar',
            $this->appAsset('/Partial/Sidebar/css/scripts.js'),
            ['jquery'],
            180511,
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
        wp_enqueue_style('tiFyPartial-Sidebar');
        wp_enqueue_script('tiFyPartial-Sidebar');
    }

    /**
     * Entête de l'interface utilisateur.
     *
     * @return string
     */
    final public function wp_head()
    {
        ?>
        <style type="text/css">
            /* = RESPONSIVE = */
            @media (min-width: <?php echo $this->appConfig('min-width');?>) {
                body.tiFySidebar-Body .tiFySidebar{display: none;}
            }

            /* = ANIMATION = */
            <?php if($this->appConfig('animated')) : ?>
            body.tiFySidebar-Body--animated .tiFySidebar,
            body.tiFySidebar-Body--animated .tiFySidebar-pushed {
                -webkit-transition: -webkit-transform 300ms cubic-bezier(0.7, 0, 0.3, 1);
                -moz-transition: -moz-transform 300ms cubic-bezier(0.7, 0, 0.3, 1);
                -ms-transition: -ms-transform 300ms cubic-bezier(0.7, 0, 0.3, 1);
                -o-transition: -o-transform 300ms cubic-bezier(0.7, 0, 0.3, 1);
                transition: transform 300ms cubic-bezier(0.7, 0, 0.3, 1);
            }
            <?php endif;?>
        </style>
        <?php
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

        if (!$this->has('attrs.id')) :
            $this->set(
                'attrs.id',
                'tiFyPartial-Sidebar--' . $this->getId()
            );
        endif;

        if (!$this->has('attrs.class')) :
            $this->set(
                'attrs.class',
                'tiFyPartial-Sidebar tiFyPartial-Sidebar--' . $this->getId()
            );
        endif;

        $this->set(
            'attrs.style',
            'width:' . $this->get('width') .';z-index:' . $this->get('z-index') . $this->get('attrs.style', '')
        )
            ->set('attrs.aria-position', $this->get('pos'))

            ->set('attrs.aria-closed', $this->get('closed') ? 'true' : 'false')

            ->set('attrs.aria-animate', $this->get('animate') ? 'true' : 'false');
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function display()
    {
        return  $this->appTemplateRender(
                'sidebar',
                [
                    'items'         => SidebarWalker::display(
                        $this->get('nodes', []),
                        [
                            'prefix' => 'tiFyPartial-Sidebar'
                        ]
                    ),
                    'html_attrs'    => Tools::Html()->parseAttrs($this->get('attrs', []))
                ]
        );
    }

    /**
     * Affichage du bouton de bascule.
     *
     * @return string

    public static function displayToggleButton($args = [])
    {
            // BOUTON DE BASCULE
            if (self::tFyAppConfig('toggle')) :
                $buttonAttrs = [
                    'pos'   => self::tFyAppConfig('pos'),
                    'class' => 'tiFySidebar-toggleButton tiFySidebar-toggleButton--' . self::tFyAppConfig('pos'),
                ];
                if (is_string(self::tFyAppConfig('toggle'))) :
                    $buttonAttrs['text'] = self::tFyAppConfig('toggle');
                endif;

                $output .= self::displayToggleButton($buttonAttrs, false);
            endif;


        $defaults = [
            'pos'   => self::tFyAppConfig('pos'),
            'text'  => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 75 75" xml:space="preserve" fill="#000" ><g><rect width="75" height="10" x="0" y="0" ry="0"/><rect width="75" height="10" x="0" y="22" ry="0"/><rect width="75" height="10" x="0" y="44" ry="0"/></g></svg>',
            'class' => '',
        ];
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $output = "";
        $output .= "<a href=\"#tify_sidebar-panel_{$pos}\"" . ($class ? " class=\"" . $class . "\"" : "") . " data-toggle=\"tiFySidebar\" data-target=\"{$pos}\">";
        $output .= $text;
        $output .= "</a>\n";

        return $output;
    }
    */
}