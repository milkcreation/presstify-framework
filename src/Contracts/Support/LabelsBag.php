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
     * Vérification du genre.
     *
     * @return boolean
     */
    public function hasGender(): bool;

    /**
     * Permet de vérifier si la première lettre d'une chaîne de caractère est une voyelle.
     *
     * @param string $string Chaîne de caractère à traiter.
     *
     * @return boolean
     */
    public function isFirstVowel(string $string): bool;

    /**
     * @inheritDoc
     */
    public function parse(): LabelsBag;

    /**
     * Définition du genre de l'élément
     *
     * @param string $gender
     *
     * @return static
     */
    public function setGender(bool $gender): LabelsBag;

    /**
     * Définition du nom de qualification.
     *
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name): LabelsBag;

    /**
     * Définition de l'intitulé du pluriel d'un élément.
     *
     * @param string $plural
     *
     * @return static
     */
    public function setPlural(string $plural): LabelsBag;

    /**
     * Définition de l'intitulé du singulier d'un élément.
     *
     * @param string $singular
     *
     * @return static
     */
    public function setSingular(string $singular): LabelsBag;
}