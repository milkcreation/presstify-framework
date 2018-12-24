<?php

namespace tiFy\Partial\Partials\Sidebar;

use Illuminate\Support\Collection;
use tiFy\Partial\PartialController;

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
class Sidebar extends PartialController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string|int $width Largeur de l'interface en px ou en %. Si l'unité de valeur n'est pas renseignée l'unité par défault est le px.
     *      @var int $z -index Profondeur de champs.
     *      @var array $attrs Liste des attributs HTML.
     *      @var string $pos Position de l'interface left (default)|right.
     *      @var bool $closed Etat de fermeture initial de l'interface.
     *      @var bool $outside_close Fermeture au clic en dehors de l'interface.
     *      @var bool $animate Activation de l'animation à l'ouverture et la fermeture.
     *      @var bool|string $toggle Activation et contenu de bouton de bascule. Si la valeur booléene active ou desactive le bouton; la valeur chaîne de caractère active et affiche la chaîne ex : <span>X</span>.
     *      @var string|int $min-width Largeur de la fenêtre du navigateur en px ou %, à partir de laquelle l'interface est active. Si l'unité de valeur n'est pas renseignée l'unité par défault est le px.
     *      @var SidebarItem[]|array $items {
     *          Liste des élements.
     *          @var string $name Nom de qualification
     *          @var string|callable $content Contenu
     *          @var array $attrs Liste des attributs HTML du conteneur.
     *          @var int $position Position de l'élément.
     *      }
     *      @var boolean|string $header Contenu de l'entête de l'interface.
     *      @var boolean|string $footer Contenu du pied de l'interface.
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
        'min-width'     => '991px',
        'items'         => [],
        'header'        => true,
        'footer'        => true,
        'toggle'        => true,
        'theme'         => 'light'
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
                    'PartialSidebar',
                    assets()->url('partial/sidebar/css/styles.css'),
                    [],
                    180511
                );
                wp_register_script(
                    'PartialSidebar',
                    assets()->url('partial/sidebar/css/scripts.js'),
                    ['jquery'],
                    180511,
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
        wp_enqueue_style('PartialSidebar');
        wp_enqueue_script('PartialSidebar');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set(
            'attrs.style',
            'width:' . $this->get('width') . ';z-index:' . $this->get('z-index') . $this->get('attrs.style', '')
        )
            ->set('attrs.data-control', 'sidebar')
            ->set('attrs.aria-animate', $this->get('animate') ? 'true' : 'false')
            ->set('attrs.aria-closed', $this->get('closed') ? 'true' : 'false')
            ->set('attrs.aria-outside_close', $this->get('outside_close') ? 'true' : 'false')
            ->set('attrs.aria-position', $this->get('pos'))
            ->set('attrs.aria-theme', $this->get('theme'));

        $items = [];
        foreach ($this->get('items', []) as $item) :
            if ($item instanceof SidebarItem) :
                $items[] = $item;
            elseif (is_array($item)) :
                $items[] = new SidebarItem($item);
            elseif (is_string($item)) :
                $item = ['content' => $item];
                $items[] = new SidebarItem($item);
            endif;
        endforeach;

        $header = $this->get('header');
        $this->set(
            'header',
            $this->isCallable($header)
                ? call_user_func($header)
                : (is_string($header) ? $header : '&nbsp;')
        );

        $footer = $this->get('footer');
        $this->set(
            'footer',
            $this->isCallable($footer)
                ? call_user_func($footer)
                : (is_string($footer) ? $footer : '&nbsp;')
        );

        $this->set(
            'items',
            (new Collection($items))->sortBy('position')->all()
        );
    }

    /**
     * Traitement de la liste des attributs par défaut.
     *
     * @return void
     */
    public function parseDefaults()
    {
        $default_class = class_info($this)->getShortName() . ' ' .
            class_info($this)->getShortName() . '--' . $this->getIndex();
        if (!$this->has('attrs.class')) :
            $this->set(
                'attrs.class',
                $default_class
            );
        else :
            $this->set(
                'attrs.class',
                sprintf(
                    $this->get('attrs.class', ''),
                    $default_class
                )
            );
        endif;
        if (!$this->get('attrs.class')) :
            $this->pull('attrs.class');
        endif;

        foreach($this->get('view', []) as $key => $value) :
            $this->viewer()->set($key, $value);
        endforeach;
    }
}