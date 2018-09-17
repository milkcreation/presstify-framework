<?php

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\MetaboxContentOptionsInterface;
use tiFy\Contracts\Wp\WpScreenInterface;

abstract class AbstractMetaboxContentOptionsController
    extends AbstractMetaboxContentController
    implements MetaboxContentOptionsInterface
{
    /**
     * CONSTRUCTEUR.
     *
     * @param WpScreenInterface $screen Ecran d'affichage.
     * @param array $attrs Liste des variables passées en arguments.
     *
     * @return void
     */
    public function __construct(WpScreenInterface $screen, $args = [])
    {
        parent::__construct($screen, $args);

        foreach ($this->settings() as $setting => $attrs) :
            if (is_numeric($setting)) :
                $setting = (string) $attrs;
                $attrs = [];
            endif;

            \register_setting($object_name, $setting, $attrs);
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function display($args = [])
    {
        return $this->viewer('display', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function settings()
    {
        return [];
    }
}