<?php

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\MetaboxDisplayOptionsInterface;
use tiFy\Contracts\Metabox\MetaboxItemInterface;

abstract class AbstractMetaboxDisplayOptionsController
    extends AbstractMetaboxDisplayController
    implements MetaboxDisplayOptionsInterface
{
    /**
     * CONSTRUCTEUR.
     *
     * @param MetaboxItemInterface $item Instance de l'élément.
     * @param array $attrs Liste des variables passées en arguments.
     *
     * @return void
     */
    public function __construct(MetaboxItemInterface $item, $args = [])
    {
        parent::__construct($item, $args);

        foreach ($this->settings() as $setting => $attrs) :
            if (is_numeric($setting)) :
                $setting = (string) $attrs;
                $attrs = [];
            endif;

            \register_setting($this->getOptionsPage(), $setting, $attrs);
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function content($args = null, $null1 = null, $null2 = null)
    {
        return parent::content($args, $null1, $null2);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionsPage()
    {
        return $this->getObjectName();
    }

    /**
     * {@inheritdoc}
     */
    public function header($args = null, $null1 = null, $null2 = null)
    {
        return parent::header($args, $null1, $null2);
    }

    /**
     * {@inheritdoc}
     */
    public function settings()
    {
        return [];
    }
}