<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\Tab;

use Exception;
use tiFy\Partial\Drivers\TabDriverInterface;

interface TabCollectionInterface
{
    /**
     * Ajout d'un élément à la collection.
     *
     * @param TabFactoryInterface|array $def
     *
     * @return static
     */
    public function add($def): TabCollectionInterface;

    /**
     * Chargement
     *
     * @return static
     *
     * @throws Exception
     */
    public function boot(): TabCollectionInterface;

    /**
     * Récupération d'un élément selon son nom de qualification.
     *
     * @param string $name
     *
     * @return TabFactoryInterface|null
     */
    public function get(string $name): ?TabFactoryInterface;

    /**
     * Récupération de la liste des éléments d'un groupe.
     *
     * @param string $parent Nom de qualification du parent.
     *
     * @return TabFactoryInterface[][]
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
     * @param TabDriverInterface $tabManager
     *
     * @return static
     */
    public function setTabManager(TabDriverInterface $tabManager): TabCollectionInterface;

    /**
     * Instance du gestionnaire associé
     *
     * @return TabDriverInterface|null
     */
    public function tabManager(): ?TabDriverInterface;

    /**
     * Traitement récursif de la liste des éléments.
     *
     * @param TabFactory[]|null $items
     * @param int $depth
     * @param string $parent Nom de qualification de l'élément parent.
     *
     * @return static
     */
    public function walkRecursiveItems(array $items, int $depth = 0, string $parent = ''): TabCollectionInterface;

    /**
     * Traitement de la liste des groupes d'élements.
     *
     * @param TabFactory[]|null $items
     *
     * @return static
     */
    public function walkGrouped(?array $items = null): TabCollectionInterface;
}