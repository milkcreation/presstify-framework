<?php declare(strict_types=1);

namespace tiFy\Contracts\Template;

use Psr\Http\Message\ServerRequestInterface;

interface FactoryHttpController extends FactoryAwareTrait
{
    /**
     * Répartition de la requête selon la méthode utilisée.
     *
     * @param ServerRequestInterface $psrRequest Instance de la requête Psr.
     *
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $psrRequest);
}