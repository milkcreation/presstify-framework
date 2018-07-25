<?php

namespace tiFy\TabMetabox;

use tiFy\TabMetabox\Controller\ContentController;

class ContentOptionsController extends ContentController
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct($object_name, $object_type, $args = [])
    {
        parent::__construct($object_name, $object_type, $args);

        foreach ($this->settings() as $setting => $attrs) :
            if (is_numeric($setting)) :
                $setting = (string) $attrs;
                $attrs = [];
            endif;

            \register_setting($object_name, $setting, $attrs);
        endforeach;
    }

    /**
     * Affichage.
     *
     * @param array $args Liste des vaiables passés en argument.
     *
     * @return string
     */
    public function display($args)
    {
        parent::display();
    }

    /**
     * Listes des options à enregistrer.
     *
     * @return array
     */
    public function settings()
    {
        return [];
    }
}