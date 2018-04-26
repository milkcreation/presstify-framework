<?php

/**
 * @name MetaTag
 * @desc Controleur d'affichage de la balise meta title de l'entête du site
 * @package presstiFy
 * @namespace \tiFy\Core\Layouts\MetaTitle
 * @version 1.1
 * @subpackage Components
 * @since 1.2.571
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Core\MetaTag;

use tiFy\App\Core;
use tiFy\Core\MetaTag\MetaTitle;

final class MetaTag extends Core
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        MetaTitle::make();
    }
}