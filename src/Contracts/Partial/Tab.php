<?php declare(strict_types=1);

namespace tiFy\Contracts\Partial;

interface Tab extends PartialDriver
{
    /**
     * Ajout d'un élément.
     *
     * @param TabFactory|array $def
     *
     * @return static
     */
    public function addItem($def): Tab;

    /**
     * Récupération du gestionnaire des éléments déclarés.
     *
     * @return TabCollection
     */
    public function getTabCollection(): TabCollection;

    /**
     * Récupération du style de l'onglet.
     *
     * @param int $depth
     *
     * @return string
     */
    public function getTabStyle(int $depth = 0): string;

    /**
     * Définition du gestionnaire des éléments déclarés.
     *
     * @param TabCollection $tabCollection
     *
     * @return static
     */
    public function setTabCollection(TabCollection $tabCollection): Tab;

    /**
     * Contrôleur de traitement de la requête XHR.
     *
     * @param array ...$args Liste dynamique de variables passés en argument dans l'url de requête
     *
     * @return array
     */
    public function xhrResponse(...$args): array;
}