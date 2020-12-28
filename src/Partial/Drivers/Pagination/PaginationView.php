<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\Pagination;

use tiFy\Partial\PartialView;

/**
 * @method PaginationQueryInterface query()
 */
class PaginationView extends PartialView
{
    /**
     * @inheritDoc
     */
    public function __call($name, $arguments)
    {
        array_push($this->mixins, 'query');

        return parent::__call($name, $arguments);
    }

    /**
     * Récupération de la page courante.
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->query()->getCurrentPage();
    }

    /**
     * Récupération de la dernière page.
     *
     * @return int
     */
    public function getLastPage(): int
    {
        return $this->query()->getLastPage();
    }
}