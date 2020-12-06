<?php declare(strict_types=1);

namespace tiFy\Contracts\Partial;

use Exception;
use tiFy\Contracts\Partial\Tab as TabManager;

interface TabCollection
{
    /**
     * Ajout d'un élément à la collection.
     *
     * @param TabFactory|array $def
     *
     * @return TabCollection
     */
    public function add($def): TabCollection;

    /**
     * Chargement
     *
     * @return static
     *
     * @throws Exception
     */
    public function boot(): TabCollection;

    /**
     * Récupération d'un élément selon son nom de qualification.
     *
     * @param string $name
     *
     * @return TabFactory|null
     */
    public function get(string $name): ?TabFactory;

    /**
     * Récupération de la liste des éléments d'un groupe.
     *
     * @param string $parent Nom de qualification du parent.
     *
     * @return TabFactory[][]
     */
    public function getGrouped(string $parent = ''): iterable;

    /**
     * Récupération de l'indice incrémenté.
     *
     * @return int
     */
    public function getIncreasedItemIdx(): int;

    /**
     * Vérification du statut de chargement.
     *
     * @return bool
     */
    public function isBooted(): bool;

    /**
     * Définition du gestionnaire associé.
     *
     * @param TabManager $tabManager
     *
     * @return static
     */
    public function setTabManager(TabManager $tabManager): TabCollection;

    /**
     * Instance du gestionnaire associé
     *
     * @return TabManager|null
     */
    public function tabManager(): ?TabManager;

    /**
     * Traitement récursif de la liste des éléments.
     *
     * @param TabFactory[]|null $items
     * @param int $depth
     * @param string $parent Nom de qualification de l'élément parent.
     *
     * @return static
     */
    public function walkRecursiveItems(array $items, int $depth = 0, string $parent = ''): TabCollection;

    /**
     * Traitement de la liste des groupes d'élements.
     *
     * @param TabFactory[]|null $items
     *
     * @return static
     */
    public function walkGrouped(?array $items = null): TabCollection;
}