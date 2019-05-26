<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

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
     * Traitement de la requête ajax (XmlHttpRequest).
     *
     * @param array $args Liste des arguments dynamique passé à la requête
     *
     * @return array
     */
    public function xhrHandler(...$args);
}