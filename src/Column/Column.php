<?php

namespace tiFy\Column;

use Illuminate\Support\Collection;
use tiFy\Contracts\Column\Column as ColumnInterface;
use tiFy\Contracts\Wp\WpScreenInterface;

final class Column implements ColumnInterface
{
    /**
     * Liste des éléments affichés sur la page courante.
     * @var ColumnItemController[]
     */
    protected $currents = [];

    /**
     * Liste des éléments déclarés.
     * @var ColumnItemController[]
     */
    protected $items = [];

    /**
     * Liste des éléments à déclarer.
     * @var array
     */
    protected $registred = [];

    /**
     * Liste des éléments à supprimer.
     * @var array
     */
    protected $unregistred = [];

    /**
     * Instance de l'écran d'affichage courant.
     * @var WpScreenInterface
     */
    protected $screen;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'wp_loaded',
            function () {
                foreach (config('column', []) as $screen => $items) :
                    foreach ($items as $name => $attrs) :
                        if (is_numeric($screen)) :
                            $_screen = isset($attrs['screen']) ? $attrs['screen'] : null;
                        else :
                            $_screen = $screen;
                        endif;

                        if (!is_null($_screen)) :
                            if (preg_match('#(.*)@(post_type|taxonomy|user)#', $_screen)) :
                                $_screen = 'list::' . $_screen;
                            endif;

                            $this->items[] = app()->resolve('column.item', [$_screen, $name, $attrs]);
                        endif;
                    endforeach;
                endforeach;
            },
            0
        );

        add_action(
            'current_screen',
            function ($wp_current_screen) {
                $this->screen = app('wp.screen', [$wp_current_screen]);

                /** @var \WP_Screen $wp_current_screen */
                foreach ($this->items as $item) :
                    $item->load($this->screen);
                endforeach;

                $this->currents = (new Collection($this->items))->filter(
                    function (ColumnItemController $item) {
                        return $item->isActive();
                    }
                );

                switch ($this->screen->getObjectType()) :
                    case 'post_type' :
                        add_filter(
                            'manage_edit-' . $this->screen->getObjectName() . '_columns',
                            [$this, 'parseColumnHeaders']
                        );

                        add_action(
                            'manage_' . $this->screen->getObjectName() . '_posts_custom_column',
                            [$this, 'parseColumnContents'],
                            25,
                            2
                        );
                        break;

                    case 'taxonomy' :
                        add_filter(
                            'manage_edit-' . $this->screen->getObjectName() . '_columns',
                            [$this, 'parseColumnHeaders']
                        );

                        add_filter(
                            'manage_' . $this->screen->getObjectName() . '_custom_column',
                            [$this, 'parseColumnContents'],
                            25,
                            3
                        );
                        break;

                    case 'user' :
                        add_filter(
                            'manage_edit-' . $this->screen->getObjectName() . '_columns',
                            [$this, 'parseColumnHeaders']
                        );

                        add_filter(
                            'manage_' . $this->screen->getObjectName() . '_custom_column',
                            [$this, 'parseColumnContents'],
                            25,
                            3
                        );
                        break;

                    default :
                        add_filter(
                            'manage_columns',
                            [$this, 'parseColumnHeaders']
                        );

                        add_filter(
                            'manage_custom_column',
                            [$this, 'parseColumnContents'],
                            25,
                            3
                        );
                        break;
                endswitch;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function add($screen, $name, $attrs = [])
    {
        config()->set(
            "column.{$screen}.{$name}",
            $attrs
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentItems()
    {
        return $this->currents;
    }

    /**
     * {@inheritdoc}
     */
    final public function parseColumnHeaders($headers)
    {
        // Traitement des colonnes système.
        $i = 0;
        foreach ($headers as $name => $title) :
            /** @var ColumnItemController $column */
            $column = app(
                'column.item',
                [
                    $this->screen,
                    $name,
                    [
                        'title'    => $title,
                        'position' => 0.99+$i++,
                    ],
                ]
            );
            $column->load($this->screen);
            $this->currents[] = $column;
        endforeach;

        // Ordonnacement
        $max = (new Collection($this->currents))->max(
            function (ColumnItemController $item) {
                return $item->getPosition();
            }
        );
        if ($max) :
            $pad = 0;
            (new Collection($this->currents))->each(
                function (ColumnItemController $item, $key) use (&$pad, $max) {
                    $position = $item->getPosition() ? : ++$pad+$max;

                    return $item->set('position', absint($position));
                }
            );
        endif;
        $this->currents = (new Collection($this->currents))->sortBy(
            function (ColumnItemController $item) {
                return $item->getPosition();
            }
        );

        // Définition des entêtes.
        $headers = [];
        foreach ($this->currents as $c) :
            $headers[$c->getName()] = $c->getHeader();
        endforeach;

        remove_filter(current_filter(), [$this, 'parseDisplayedHeaders']);

        return $headers;
    }

    /**
     * {@inheritdoc}
     */
    final public function parseColumnContents()
    {
        foreach ($this->currents as $c) :
            $echo = false;

            switch ($this->screen->getObjectType()) :
                case 'post_type' :
                    $column_name = func_get_arg(0);
                    $echo = true;

                    if ($column_name !== $c->getName()) :
                        continue 2;
                    endif;
                    break;

                case 'taxonomy' :
                    $output = func_get_arg(0);
                    $column_name = func_get_arg(1);

                    if ($column_name !== $c->getName()) :
                        continue 2;
                    endif;
                    break;

                case 'custom' :
                    $output = func_get_arg(0);
                    $column_name = func_get_arg(1);

                    if ($column_name !== $c->getName()) :
                        continue 2;
                    endif;
                    break;
            endswitch;

            $content = $c->getContent();
            $output = call_user_func_array($content, func_get_args());

            if ($echo) :
                echo $output;
                break;
            else :
                return $output;
            endif;
        endforeach;
    }

}