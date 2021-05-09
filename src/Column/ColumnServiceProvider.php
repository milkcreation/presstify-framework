<?php

declare(strict_types=1);

namespace tiFy\Column;

use Pollen\Container\BaseServiceProvider;

class ColumnServiceProvider extends BaseServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        'column'
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('column', function () {
            return new Column();
        });
    }
}