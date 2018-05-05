<?php

/**
 * @name MetaTag
 * @desc Controleur d'affichage de la balise meta title de l'entête du site
 * @package presstiFy
 * @namespace \tiFy\Partials\MetaTitle
 * @version 1.1
 * @subpackage Components
 * @since 1.2.571
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\MetaTag;

use tiFy\Apps\AppController;

final class MetaTag extends AppController
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->appServiceShare(MetaTitle::class);
        $this->appServiceShare(Favicon::class);
    }

    /**
     * Traitement de la balise titre
     *
     * @return MetaTitle
     */
    public function title()
    {
        return $this->appServiceGet(MetaTitle::class);
    }

    /**
     * Traitement de la balise titre
     *
     * @return MetaTitle
     */
    public function favicon()
    {
        return $this->appServiceGet(Favicon::class);
    }
}