<?php

namespace tiFy\Column;

use League\Container\Exception\NotFoundException;
use tiFy\Apps\AppController;
use tiFy\Column\ObjectNameFactory;
use tiFy\tiFy;

final class Column extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('init');
        $this->appAddAction('admin_init', null, 99);
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        // Traitement des colonnes déclarées dans le fichier configuration
        if ($config = $this->appConfig()) :
            foreach ($this->appConfig() as $object_type => $object_names) :
                if (!is_array($object_names)) :
                    continue;
                endif;

                foreach ($object_names as $object_name => $custom_columns) :
                    foreach ($custom_columns as $name => $args) :
                        $this
                            ->get($object_type, $object_name)
                            ->add($name, $args);
                    endforeach;
                endforeach;
            endforeach;
        endif;
    }

    /**
     * Initialisation de l'interface d'administration.
     *
     * @return void
     */
    public function admin_init()
    {
        do_action('tify_column_register', $this);
    }

    /**
     * Récupération du controleur de type d'objet
     *
     * @param $object_type
     * @param $object_name
     *
     * @return ObjectNameFactory
     */
    public function get($object_type, $object_name)
    {
        // Définition du controleur
        switch($object_type) :
            case 'post_type' :
            case 'taxonomy' :
            case 'custom' :
                $controller = ObjectNameFactory::class;
                break;
            default :
                wp_die(
                    sprintf(
                        __('Ce type de colonne %s n\'est pas valide. Seules les types %s sont permis.', 'tify'),
                        $object_type,
                        join(', ', ['post_type', 'taxonomy', 'custom'])
                    ),
                    __('tiFy\Column : Ajout de colonne impossible', 'tify'),
                    500
                );
                break;
        endswitch;

        // Récupération du conteneur de type d'objet
        $id = "tify.column.{$object_type}.{$object_name}";

        if (!$this->appServiceHas($id)) :
            $this->appServiceShare($id, new $controller($object_type, $object_name));
        endif;

        return $this->appServiceGet($id);
    }

    /**
     * Récupération du controleur de type d'objet
     *
     * @param $object_type
     * @param $object_name
     *
     * @return ObjectNameFactory
     */
    public static function make($object_type, $object_name)
    {
        try {
            /** @var Column $Column Controleur de colonnes tiFy */
            $Column = self::appInstance();
        } catch (NotFoundException $e) {
            wp_die(
                $e->getMessage(),
                __(__CLASS__ . ' - Contrôleur principal introuvable', 'tify'),
                $e->getCode()
            );
        }

        return $Column->get($object_type, $object_name);
    }
}