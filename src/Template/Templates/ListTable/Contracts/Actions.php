<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use Exception;
use tiFy\Contracts\Template\FactoryActions;

interface Actions extends FactoryActions
{
    /**
     * Activation d'éléments.
     * @todo
     *
     * @throws Exception
     */
    public function doActivate();

    /**
     * Désactivation d'éléments.
     * @todo
     *
     * @throws Exception
     */
    public function doDeactivate();

    /**
     * Suppression d'éléments.
     * @todo
     *
     * @throws Exception
     */
    public function doDelete();

    /**
     * Duplication d'éléments.
     * @todo
     *
     * @throws Exception
     */
    public function doDuplicate();

    /**
     * Mise à la corbeille d'éléments.
     * @todo
     *
     * @throws Exception
     */
    public function doTrash();

    /**
     * Restauration d'éléments.
     * @todo
     *
     * @throws Exception
     */
    public function doUntrash();
}