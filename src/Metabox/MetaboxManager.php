<?php declare(strict_types=1);

namespace tiFy\Metabox;

use tiFy\Contracts\{
    Metabox\MetaboxFactory,
    Metabox\MetaboxManager as MetaboxManagerContract
};
use tiFy\Support\Manager;

class MetaboxManager extends Manager implements MetaboxManagerContract
{
    /**
     * Liste des éléments déclarés.
     * @var MetaboxFactory[]
     */
    protected $items = [];

    /**
     * Liste des éléments à supprimer.
     * @var array
     */
    protected $removes = [];

    /**
     * Liste des boîtes à onglets à personnaliser.
     * @var array
     */
    protected $tabs = [];

    /**
     * @inheritDoc
     */
    public function add($name, string $screen, ...$args)
    {
        /*if ($args[0]) {
            if (is_string($args[0])) {
                $context = $args[0];
            }
        }

        if (empty($screen)) {
            $screen = '';
        }

        config()->set("metabox.{$screen}", array_merge([$name => $attrs], config("metabox.{$screen}", [])));*/

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove($id, $screen = null, $context = 'normal')
    {
        if (!$screen) {
            $screen = '';
        }

        if (!isset($this->removes[$screen])) {
            $this->removes[$screen] = [];
        }

        if (!isset($this->removes[$screen][$id])) {
            $this->removes[$screen][$id] = [];
        }

        array_push($this->removes[$screen][$id], $context);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function tab($attrs = [], $screen = null)
    {
        if (!$screen) {
            $screen = '';
        }

        $this->tabs[$screen] = $attrs;

        return $this;
    }
}