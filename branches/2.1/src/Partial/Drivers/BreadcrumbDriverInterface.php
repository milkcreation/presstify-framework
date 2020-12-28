<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\Drivers\Breadcrumb\BreadcrumbCollectionInterface;
use tiFy\Partial\PartialDriverInterface;

interface BreadcrumbDriverInterface extends PartialDriverInterface
{
    /**
     * Ajout de la déclaration d'un élément.
     *
     * @param string|array|object $item
     *
     * @return int|null
     */
    public function add($item): ?int;

    /**
     * Ajout de la déclaration d'un élément (alias).
     * @see static::add()
     *
     * @param string|array|object $item
     *
     * @return int|null
     */
    public function append($item): ?int;

    /**
     * Récupération de l'instance du gestion de collection d'éléments.
     *
     * @return BreadcrumbCollectionInterface
     */
    public function collection(): BreadcrumbCollectionInterface;

    /**
     * Désactivation de l'affichage.
     *
     * @return static
     */
    public function disable(): BreadcrumbDriverInterface;

    /**
     * Activation de l'affichage.
     *
     * @return static
     */
    public function enable(): BreadcrumbDriverInterface;


    /**
     * Réinitialisation de la liste des éléments déclarés.
     *
     * @return static
     */
    public function flush(): BreadcrumbDriverInterface;

    /**
     * Récupération de l'instance principale.
     *
     * @return BreadcrumbDriverInterface
     */
    public function main(): BreadcrumbDriverInterface;

    /**
     * Traitemenent d'un élément
     *
     * @param string|array|object $item
     *
     * @return array|null
     */
    public function parseItem($item): ?array;

    /**
     * Pré-Récupération de la collection d'éléments du fil d'ariane.
     *
     * @return static
     */
    public function prefetch(): BreadcrumbDriverInterface;

    /**
     * Ajout de la déclaration d'un élément au début de la liste.
     *
     * @param string|array|object $item
     *
     * @return int|null
     */
    public function prepend($item): ?int;

    /**
     * Vérification d'activation.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Insertion d'un élément déclaré à une position déterminée.
     *
     * @param int $position
     * @param string|array|object $item
     *
     * @return int|null
     */
    public function insert(int $position, $item): ?int;

    /**
     * Déplace un élément déclaré vers une position déterminée.
     *
     * @param int $from Position actuelle de l'élément à déplacer.
     * @param int $to Position attendue de l'élément.
     *
     * @return int|null
     */
    public function move(int $from, int $to): ?int;

    /**
     * Supprime un élément déclaré selon sa position.
     *
     * @param int $position
     *
     * @return BreadcrumbDriverInterface
     */
    public function remove(int $position): BreadcrumbDriverInterface;

    /**
     * Remplace un élément déclaré par un autre selon une position déterminée.
     *
     * @param int $position
     * @param string|array|object $item
     *
     * @return int|null
     */
    public function replace(int $position, $item): ?int;
}