<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\Routing\Route;
use tiFy\Contracts\Support\ParamsBag;

interface Ajax extends ParamsBag
{
    /**
     * Récupération de la liste des colonnes.
     *
     * @return array
     */
    public function getColumns(): array;

    /**
     * Récupération de la liste des translations.
     *
     * @return array
     */
    public function getLanguage(): array;

    /**
     * Traitement de la liste des options.
     *
     * @return array
     */
    public function parseOptions(array $options = []): array;

    /**
     * Définition de la route XHR associée.
     *
     * @param Route $route
     *
     * @return static
     */
    public function setXhr(Route $route): Ajax;

    /**
     * Traitement de la requête ajax (XmlHttpRequest).
     *
     * @return array
     */
    public function xhrHandler(ServerRequestInterface $psrRequest);
}