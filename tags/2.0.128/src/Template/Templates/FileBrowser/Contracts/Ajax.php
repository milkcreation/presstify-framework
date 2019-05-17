<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileBrowser\Contracts;

use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\Template\FactoryAwareTrait;

interface Ajax extends FactoryAwareTrait, ParamsBag
{
    /**
     * Traitement de la requête Ajax
     *
     * @return mixed
     */
    public function handler(...$args);
}