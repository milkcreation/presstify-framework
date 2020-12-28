<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\CurtainMenu;

use tiFy\Partial\Drivers\CurtainMenuDriverInterface;
use tiFy\Contracts\Support\Collection;

interface CurtainMenuCollectionInterface extends Collection
{
    /**
     * Récupération de la liste des éléments associés à un parent.
     *
     * @param string|null $parent Nom de qualification du parent associé.
     *
     * @return CurtainMenuItem[]|array
     */
    public function getParentItems(?string $parent = null): array;

    /**
     * Préparation de l'instance.
     *
     * @param CurtainMenuDriverInterface $partial Instance du constructeur de portion d'affichage associé.
     *
     * @return static
     */
    public function prepare(CurtainMenuDriverInterface $partial): CurtainMenuCollectionInterface;

    /**
     * Préparation de la liste des éléments.
     *
     * @param CurtainMenuItem[] $items Liste des éléments à traiter.
     * @param int $depth Niveau de profondeur.
     * @param string|null $parent Élément parent.
     *
     * @return static
     */
    public function prepareItems(array $items = [], int $depth = 0, ?string $parent = null): CurtainMenuCollectionInterface;
}
