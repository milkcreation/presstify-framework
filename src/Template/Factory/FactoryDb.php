<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use Illuminate\Database\Eloquent\Model;
use tiFy\Contracts\Template\{FactoryDb as FactoryDbContract, TemplateFactory};

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class FactoryDb extends Model implements FactoryDbContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $factory;
}