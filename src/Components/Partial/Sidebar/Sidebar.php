<?php

namespace tiFy\Components\Partial\Sidebar;

use Illuminate\Support\Collection;
use tiFy\Kernel\Tools;
use tiFy\Partial\AbstractPartialItem;
use tiFy\Partial\Partial;

/**
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
class Sidebar extends AbstractPartialItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string|int $width Largeur de l'interface en px ou en %. Si l'unité de valeur n'est pas renseignée l'unité par défault est le px.
     *      @var int $z -index Profondeur de champs.
     *      @var attrs $attrs Liste des attributs HTML.
     *      @var string $pos Position de l'interface left (default)|right.
     *      @var bool $closed Etat de fermeture initial de l'interface.
     *      @var bool $outside_close Fermeture au clic en dehors de l'interface.
     *      @var bool $animate Activation de l'animation à l'ouverture et la fermeture.
     *      @var bool|string $toggle Activation et contenu de bouton de bascule. Si la valeur booléene active ou desactive le bouton; la valeur chaîne de caractère active et affiche la chaîne ex : <span>X</span>.
     *      @var string|int $min-width Largeur de la fenêtre du navigateur en px ou %, à partir de laquelle l'interface est active. Si l'unité de valeur n'est pas renseignée l'unité par défault est le px.
     *      @var SidebarItemController[]|array $items {
     *          Liste des élements.
     *          @var string $name Nom de qualification
     *          @var string|callable $content Contenu
     *          @var array $attrs Liste des attributs HTML du conteneur.
     *          @var int $position Position de l'élément.
     *      }
     *      @var string|callable $header Contenu de l'entête de l'interface.
     *      @var string|callable $footer Contenu du pied de l'interface.
     *      @var string $theme Theme couleur de l'interface light|dark.
     * }
     */
    protected $attributes = [
        'width'         => '300px',
        'z-index'       => 99990,
        'attrs'         => [],
        'pos'           => 'left',
        'closed'        => true,
        'outside_close' => true,
        'animate'       => true,
        'toggle'        => true,
        'min-width'     => '991px',
        'items'         => [],
        'header'        => '',
        'footer'        => '',
        'theme'         => 'light'
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
            $this->appAssetUrl('/Partial/Sidebar/css/styles.css'),
            [],
            180511
        );
        \wp_register_script(
            'tiFyPartialSidebar',
            $this->appAssetUrl('/Partial/Sidebar/css/scripts.js'),
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
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    public function parse($attrs = [])
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
            'width:' . $this->get('width') . ';z-index:' . $this->get('z-index') . $this->get('attrs.style', '')
        )
            ->set('attrs.aria-control', 'sidebar')
            ->set('attrs.aria-animate', $this->get('animate') ? 'true' : 'false')
            ->set('attrs.aria-closed', $this->get('closed') ? 'true' : 'false')
            ->set('attrs.aria-outside_close', $this->get('outside_close') ? 'true' : 'false')
            ->set('attrs.aria-position', $this->get('pos'))
            ->set('attrs.aria-theme', $this->get('theme'));

        $items = [];
        foreach ($this->get('items', []) as $name => $item) :
            if ($item instanceof SidebarItemController) :
                $items[] = $item;
            else :
                $items[] = new SidebarItemController($item);
            endif;
        endforeach;

        $this->set(
            'items',
            (new Collection($items))->sortBy('position')->all()
        );

        if ($this->get('toggle', true)) :
            $toggle = Partial::Tag(
                [
                    'tag'     => 'a',
                    'attrs'   => [
                        'href'         => '#' . $this->get('attrs.id'),
                        'class'        => 'tiFyPartial-SidebarToggleButton',
                        'aria-control' => 'toggle_sidebar',
                        'data-toggle'  => '#' . $this->get('attrs.id')
                    ],
                    'content' => ($this->get('theme') === 'light')
                        ? '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 75 75" xml:space="preserve" fill="#2B2B2B"><g><rect width="75" height="10" x="0" y="0" ry="0"/><rect width="75" height="10" x="0" y="22" ry="0"/><rect width="75" height="10" x="0" y="44" ry="0"/></g></svg>'
                        : '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 75 75" xml:space="preserve" fill="#FFF"><g><rect width="75" height="10" x="0" y="0" ry="0"/><rect width="75" height="10" x="0" y="22" ry="0"/><rect width="75" height="10" x="0" y="44" ry="0"/></g></svg>'
                ]
            );
            $this->set('toggle', $toggle);
        endif;

        $this->appTemplateMacro('header', [$this, 'header']);
        $this->appTemplateMacro('footer', [$this, 'footer']);
        $this->appTemplateMacro('toggle', [$this, 'toggle']);
    }

    /**
     * Affichage de l'entête.
     *
     * @return string
     */
    public function header()
    {
        $header = $this->get('header', '');

        return $this->isCallable($header) ? call_user_func($header) : $header;
    }

    /**
     * Affichage du pied.
     *
     * @return string
     */
    public function footer()
    {
        $footer = $this->get('footer', '');

        return $this->isCallable($footer) ? call_user_func($footer) : $footer;
    }

    /**
     * Affichage du bouton de bascule.
     *
     * @return string
     */
    public function toggle()
    {
        if ($toggle = $this->get('toggle')) :
            return $toggle;
        endif;
    }
}