<?php

declare(strict_types=1);

namespace tiFy\Metabox;

/**
 * @mixin \tiFy\Support\Concerns\BootableTrait
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 * @mixin MetaboxAwareTrait
 */
interface MetaboxScreenInterface
{
    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): MetaboxScreenInterface;

    /**
     * Récupération de l'alias de qualification.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Vérifie si la page courante correspond à l'écran.
     *
     * @return bool
     */
    public function isCurrent(): bool;

    /**
     * Vérifie si la route courante correspond à l'écran.
     *
     * @return bool
     */
    public function isCurrentRoute(): bool;

    /**
     * Vérifie si la requête courante correspond à l'écran.
     *
     * @return bool
     */
    public function isCurrentRequest(): bool;

    /**
     * Définition du nom de qualification.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): MetaboxScreenInterface;
}