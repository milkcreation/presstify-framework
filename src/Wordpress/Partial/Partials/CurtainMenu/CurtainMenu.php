<?php declare(strict_types=1);

namespace tiFy\Wordpress\Partial\Partials\CurtainMenu;

use tiFy\Contracts\Partial\PartialFactory as BasePartialFactoryContract;
use tiFy\Partial\Partials\CurtainMenu\{CurtainMenu as BaseCurtainMenu, CurtainMenuItems};
use tiFy\Support\Proxy\Partial as ptl;
use tiFy\Wordpress\Contracts\Partial\{CurtainMenu as CurtainMenuContract, PartialFactory as PartialFactoryContract};
use WP_Query;
use WP_Term;
use WP_Term_Query;

class CurtainMenu extends BaseCurtainMenu implements CurtainMenuContract
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        add_action('init', function () {
            wp_register_style(
                'PartialCurtainMenu',
                asset()->url('partial/curtain-menu/css/styles.css'),
                [],
                190717
            );
            wp_register_style(
                'PartialCurtainMenu',
                asset()->url('partial/curtain-menu/js/scripts.js'),
                ['jquery-ui-widget'],
                190717
            );
        });
    }

    /**
     * @inheritDoc
     */
    public function enqueue(): PartialFactoryContract
    {
        wp_enqueue_style('PartialCurtainMenu');
        wp_enqueue_script('PartialCurtainMenu');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseItems(): BasePartialFactoryContract
    {
        $items = $this->get('items', []);
        if ($items instanceof WP_Query) {
            // @todo
        } elseif ($items instanceof WP_Term_Query) {
            if (!empty($items->query_vars['child_of'])) {
                $parent = (string)$items->query_vars['child_of'];
            } else {
                $parent = null;
            }
            $terms = $items->terms;

            $_items = [];
            array_walk($terms, function (WP_Term $t) use (&$_items, $parent) {
                $_parent = (string)$t->parent;
                $url = get_term_link($t);

                $_items[(string)$t->term_id] = [
                    'nav'    => $t->name,
                    'parent' => !empty($_parent) && ($_parent !== $parent) ? (string)$t->parent : null,
                    'title'  => (string)ptl::get('tag', [
                        'attrs'   => [
                            'href' => $url,
                        ],
                        'content' => $t->name,
                        'tag'     => 'a',
                    ]),
                    'url'    => $url,
                ];
            });
            $items = new CurtainMenuItems($_items, $this->get('selected'));
        } elseif (!$items instanceof CurtainMenuItems) {
            $items = new CurtainMenuItems($items, $this->get('selected'));
        }
        $this->set('items', $items->prepare($this));

        return $this;
    }
}