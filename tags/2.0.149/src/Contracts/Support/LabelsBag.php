<?php declare(strict_types=1);

namespace tiFy\Contracts\Support;

interface LabelsBag extends ParamsBag
{
    /**
     * Récupération du déterminant de qualification d'une chaîne de caractère.
     *
     * @param string $string Chaîne de caractère à traiter.
     *
     * @return string
     */
    public function getDeterminant(string $string): string;

    /**
     * Vérification du genre.
     *
     * @return boolean
     */
    public function hasGender(): bool;

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération de la forme plurielle.
     *
     * @return string
     */
    public function getPlural(): string;

    /**
     * Récupération de la forme singulière.
     *
     * @return string
     */
    public function getSingular(): string;

    /**
     * Permet de vérifier si la première lettre d'une chaîne de caractère est une voyelle.
     *
     * @param string $string Chaîne de caractère à traiter.
     *
     * @return boolean
     */
    public function isFirstVowel(string $string): bool;
}