<?php

/**
 * @name MetaTag
 * @desc Controleur d'affichage de la balise meta title de l'entête du site
 * @package presstiFy
 * @namespace \tiFy\Core\Partials\MetaTitle
 * @version 1.1
 * @subpackage Components
 * @since 1.2.571
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Core\MetaTag;

use League\Container\Exception\NotFoundException;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Core\MetaTag\MetaTitle;

final class MetaTag
{
    use TraitsApp;

    /**
     * Récupération de l'instance de la classe.
     *
     * @return object|self
     */
    public static function get()
    {
        if (! self::tFyAppHasContainer(__CLASS__)) :
            wp_die('L\'appel de l\'instance doit se faire après l\'événement "after_setup_tify".', 'tify');
        endif;

        try {
            return self::tFyAppGetContainer(__CLASS__);
        } catch(NotFoundException $e) {
            wp_die($e->getMessage(), '', $e->getCode());
            exit;
        }
    }

    /**
     * Traitement de la balise titre
     *
     * @return MetaTitle
     */
    public function title()
    {
        return MetaTitle::make();
    }

    /**
     * Traitement de la balise titre
     *
     * @return MetaTitle
     */
    public function favicon()
    {
        return MetaTitle::make();
    }
}