<?php declare(strict_types=1);

namespace tiFy\Contracts\Field;

interface Suggest extends FieldFactory
{
    /**
     * @inheritDoc
     */
    public function getUrl(): string;

    /**
     * @inheritDoc
     */
    public function prepareRoute(): FieldFactory;

    /**
     * Traitement de la réponse Xhr de récupération des éléments associés.
     *
     * @param array ...$args Liste dynamique de variables passés en argument dans l'url de requête
     *
     * @return array
     */
    public function xhrResponse(...$args): array;
}