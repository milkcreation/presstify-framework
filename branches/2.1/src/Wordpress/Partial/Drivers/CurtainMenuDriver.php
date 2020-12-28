<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Partial\Drivers;

use tiFy\Partial\Drivers\CurtainMenu\CurtainMenuCollection;
use tiFy\Partial\Drivers\CurtainMenu\CurtainMenuCollectionInterface;
use tiFy\Partial\Drivers\CurtainMenuDriver as BaseCurtainMenuDriver;
use tiFy\Partial\Drivers\CurtainMenuDriverInterface as BaseCurtainMenuDriverInterface;
use tiFy\Support\Proxy\Partial as ptl;
use WP_Query;
use WP_Term;
use WP_Term_Query;

class CurtainMenuDriver extends BaseCurtainMenuDriver
{
    /**
     * @inheritDoc
     */
    public function parseItems(): BaseCurtainMenuDriverInterface
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
            array_walk(
                $terms,
                function (WP_Term $t) use (&$_items, $parent) {
                    $_parent = (string)$t->parent;
                    $url = get_term_link($t);

                    $_items[(string)$t->term_id] = [
                        'nav'    => $t->name,
                        'parent' => !empty($_parent) && ($_parent !== $parent) ? (string)$t->parent : null,
                        'title'  => (string)ptl::get(
                            'tag',
                            [
                                'attrs'   => [
                                    'href' => $url,
                                ],
                                'content' => $t->name,
                                'tag'     => 'a',
                            ]
                        ),
                        'url'    => $url,
                    ];
                }
            );
            $items = new CurtainMenuCollection($_items, $this->get('selected'));
        } elseif (!$items instanceof CurtainMenuCollectionInterface) {
            $items = new CurtainMenuCollection($items, $this->get('selected'));
        }
        $this->set('items', $items->prepare($this));

        return $this;
    }
}