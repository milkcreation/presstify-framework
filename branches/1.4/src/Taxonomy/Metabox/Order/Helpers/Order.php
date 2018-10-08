<?php

namespace tiFy\Core\Taboox\Taxonomy\Order\Helpers;

class Order extends \tiFy\App
{
    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Déclaration des fonctions d'aide à la saisie
        $this->appAddHelper('tify_taboox_term_order_get', 'get');
    }

    /**
     * Récupération de la liste des éléments
     *
     * @param int Identifiant de qualification du post
     * @param array $args Liste des attributs de récupération
     *
     * @return array Liste des identifiants de qualification des posts en relation
     */
    public static function get($taxonomy, $args = [])
    {

    }
}

