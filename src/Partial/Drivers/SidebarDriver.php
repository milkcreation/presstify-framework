<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use Closure;
use Illuminate\Support\Collection;
use tiFy\Partial\Drivers\Sidebar\SidebarItem;
use tiFy\Partial\PartialDriver;
use tiFy\Partial\PartialDriverInterface;
use tiFy\Support\Arr;

/**
 * RESSOURCES POUR EVOLUTION :
 * @see http://mango.github.io/slideout/
 * @see http://webdesignledger.com/web-design-2/best-practices-for-hamburger-menus
 * http://tympanus.net/Blueprints/SlidePushMenus/
 * http://tympanus.net/Development/OffCanvasMenuEffects/
 * http://tympanus.net/Development/MultiLevelPushMenu/
 */
class SidebarDriver extends PartialDriver implements SidebarDriverInterface
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var string|int $width Largeur de l'interface en px ou en %. Si l'unité de valeur n'est pas renseignée
             * l'unité par défault est le px.
             */
            'width'         => '300px',
            /**
             * @var int $z-index Profondeur de champs.
             */
            'z-index'       => 99990,
            /**
             * @var string $pos Position de l'interface left (default)|right.
             */
            'pos'           => 'left',
            /**
             * @var bool $closed Etat de fermeture initial de l'interface.
             */
            'closed'        => true,
            /**
             * @var bool $outside_close Fermeture au clic en dehors de l'interface.
             */
            'outside_close' => true,
            /**
             * @var bool $animate Activation de l'animation à l'ouverture et la fermeture.
             */
            'animate'       => true,
            /**
             * @var string|int $min-width Largeur de la fenêtre du navigateur en px ou %, à partir de laquelle
             * l'interface est active. Si l'unité de valeur n'est pas renseignée l'unité par défault est le px.
             */
            'min-width'     => '991px',
            /**
             * @var bool|string $header Contenu de l'entête de l'interface.
             */
            'header'        => true,
            /**
             * @var SidebarItem[]|array $body {
             * Liste des élements.
             * @var string $name Nom de qualification
             * @var string|callable $content Contenu
             * @var array $attrs Liste des attributs HTML du conteneur.
             * @var int $position Position de l'élément.
             * }
             */
            'body'          => [],
            /**
             * @var bool|string $footer Contenu du pied de l'interface.
             */
            'footer'        => true,
            /**
             * @var bool|string|array $toggle Activation et contenu de bouton de bascule. Si la valeur booléene active
             * ou désactive le bouton; la valeur chaîne de caractère active et affiche la chaîne.
             * ex : <span>X</span>.
             */
            'toggle'        => true,
            /**
             * @var string $theme Theme couleur de l'interface light|dark.
             */
            'theme'         => 'light',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverInterface
    {
        parent::parseParams();

        $style = $this->get('attrs.style');
        if ($width = $this->get('width')) {
            $style .= rtrim(';', $style) . ";width:{$width}";
        }

        if ($zindex = $this->get('z-index')) {
            $style .= rtrim(';', $style) . ";z-index:{$zindex}";
        }

        $this
            ->set('attrs.style', $style)
            ->set('attrs.data-control', 'sidebar')
            ->set('attrs.aria-animate', $this->get('animate') ? 'true' : 'false')
            ->set('attrs.aria-closed', $this->get('closed') ? 'true' : 'false')
            ->set('attrs.aria-outside_close', $this->get('outside_close') ? 'true' : 'false')
            ->set('attrs.aria-position', $this->get('pos'))
            ->set('attrs.aria-theme', $this->get('theme'));

        $body = $this->get('body', []);
        if (is_array($body)) {
            $items = [];

            foreach ($this->get('items', []) as $name => $item) {
                if ($item instanceof SidebarItem) {
                    $items[] = $item;
                } elseif (is_array($item)) {
                    $items[] = new SidebarItem((string)$name, $item);
                } elseif (is_string($item) || ($item instanceof Closure)) {
                    $item = ['content' => $item];
                    $items[] = new SidebarItem((string)$name, $item);
                }
            }
        } elseif (is_string($body) || ($body instanceof Closure)) {
            $items = [new SidebarItem('default', ['content' => $body])];
        }

        if ($header = $this->get('header')) {
            $this->set('header', $header instanceof Closure
                ? call_user_func($header) : (is_string($header) ? $header : '&nbsp;'));
        }

        if ($footer = $this->get('footer')) {
            $this->set('footer', $footer instanceof Closure
                ? call_user_func($footer) : (is_string($footer) ? $footer : '&nbsp;'));
        }

        $this->set('items', (new Collection($items))->sortBy('position')->all());

        if ($toggle = $this->get('toggle')) {
            if (is_string($toggle)) {
                $attrs = ['content' => $toggle];
            } elseif (is_array($toggle)) {
                $attrs = $toggle;
            } else {
                $attrs = [];
            }

            if ($class = Arr::get($attrs, 'attrs.class', '') ?: '%s') {
                Arr::set($attrs, 'attrs.class', sprintf($class, 'Sidebar-toggle'));
            }

            $this->set('toggle', $this->toggle($attrs));
        }

        return $this;
    }

    /**
     * Lien de bascule d'affichage de la sidebar.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return string
     */
    public function toggle(array $attrs = []): string
    {
        $id = $this->get('attrs.id');
        $attrs = array_merge([
            'attrs'   => [
                'class' => 'Sidebar-toggle',
            ],
            'tag'     => 'a',
            'content' => "<svg xmlns=\"http://www.w3.org/2000/svg\"" .
                " viewBox=\"0 0 50 50\" xml:space=\"preserve\" class=\"Sidebar-toggleSvg\">" .
                "<rect width=\"50\" height=\"5\" x=\"0\" y=\"5\" ry=\"0\"/>" .
                "<rect width=\"50\" height=\"5\" x=\"0\" y=\"22.5\" ry=\"0\"/>" .
                "<rect width=\"50\" height=\"5\" x=\"0\" y=\"40\" ry=\"0\"/></svg>",
        ], $attrs);

        if ((Arr::get($attrs, 'tag') === 'a') && !Arr::has($attrs, 'attrs.href')) {
            Arr::set($attrs, 'attrs.href', "#{$id}");
        }

        Arr::set($attrs, 'attrs.data-control', 'sidebar.toggle');

        if ($id) {
            Arr::set($attrs, 'attrs.data-target', "#{$id}");
        }

        return $this->view('toggle', compact('attrs'));
    }
}