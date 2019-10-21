<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Support\Collection as Collection;
use tiFy\Contracts\Template\FactoryAwareTrait;

interface Items extends FactoryAwareTrait, Collection
{
    /**
     * Récupération du nombre total d'éléments trouvés.
     *
     * @return int
     */
    public function total(): int;
}