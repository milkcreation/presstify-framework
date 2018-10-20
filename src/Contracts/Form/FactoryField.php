<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Kernel\ParamsBagInterface;
use tiFy\Contracts\Form\FormResolver;

interface FactoryField extends FormResolver, ParamsBagInterface
{
    /**
     * Résolution de sortie de l'affichage.
     *
     * @return string
     */
    public function __toString();

    /**
     * Récupération d'un ou de la liste des attributs de configuration complémentaires.
     *
     * @param null|string $key Clé d'indexe de l'attribut. Syntaxe à point permise. Laisser à null (défaut) pour récupérer la liste complète.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getExtras($key = null, $default = null);

    /**
     * Récupération du groupe d'appartenance.
     *
     * @return int
     */
    public function getGroup();

    /**
     * Récupération de l'indice de qualification de la variable de requête.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de l'ordre d'affichage.
     *
     * @return int
     */
    public function getPosition();

    /**
     * Récupération de l'identifiant de qualification.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Récupération du type.
     *
     * @return string
     */
    public function getType();

    /**
     * Récupération de la valeur.
     *
     * @param boolean Activation de la valeur de retour au format brut.
     *
     * @return mixed
     */
    public function getValue($raw = false);

    /**
     * Récupération de la liste des valeurs.
     *
     * @param bool $raw Activation de la valeur de retour au format brut.
     * @param null|string $glue Caractère d'assemblage de la valeur.
     *
     * @return string|array
     */
    public function getValues($raw = false, $glue = ', ');

    /**
     * Vérification d'existance d'une étiquette.
     *
     * @return boolean
     */
    public function hasLabel();

    /**
     * Vérification d'une propriété de support.
     *
     * @param string $support Propriété du support à vérifier.
     *
     * @return boolean
     */
    public function hasSupport($support);

    /**
     * Vérification de l'encapsulation du champ.
     *
     * @return boolean
     */
    public function hasWrapper();

    /**
     * Définition d'une attributs de configuration complémentaire.
     *
     * @param string $key Clé d'indexe de l'attribut.
     * @param mixed $value Valeur à définir.
     *
     * @return array
     */
    public function setExtra($key, $value);

    /**
     * Définition de l'ordre d'affichage.
     *
     * @param int $position Valeur de la position.
     *
     * @return $this
     */
    public function setPosition($position = 0);

    /**
     * Définition d'une propriété de support.
     *
     * @param string $key Clé d'indexe du support.
     * @param mixed $value Valeur du support.
     *
     * @return $this
     */
    public function setSupport($key, $value);

    /**
     * Définition de la valeur d'un champ.
     *
     * @param mixed $value Valeur à définir.
     *
     * @return $this
     */
    public function setValue($value);
}