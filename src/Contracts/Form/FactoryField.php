<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Support\ParamsBag;

interface FactoryField extends FactoryResolver, ParamsBag
{
    /**
     * Résolution de sortie de l'affichage.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Définition d'un message d'erreur associé au champ.
     *
     * @param string $message
     * @param array $datas Liste des données associées au message.
     *
     * @return static
     */
    public function addError(string $message, array $datas = []): FactoryField;

    /**
     * Récupération du pré-affichage du champ.
     *
     * @return string
     */
    public function after(): string;

    /**
     * Récupération du post-affichage du champ.
     *
     * @return string
     */
    public function before(): string;

    /**
     * Récupération d'attributs d'addon.
     * {@internal Retourne la liste complète si $key est à null.}
     *
     * @param string $name Nom de qualification de l'addon.
     * @param null|string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getAddonOption(string $name, ?string $key = null, $default = null);

    /**
     * Récupération de l'instance du contrôleur de champ.
     *
     * @return FieldController
     */
    public function getController(): FieldController;

    /**
     * Récupération d'un ou de la liste des attributs de configuration complémentaires.
     *
     * @param string|null $key Clé d'indexe de l'attribut. Syntaxe à point permise. Laisser à null (défaut) pour
     *                         récupérer la liste complète.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getExtras(?string $key = null, $default = null);

    /**
     * Récupération du groupe d'appartenance.
     *
     * @return FactoryGroup
     */
    public function getGroup(): ?FactoryGroup;

    /**
     * Récupération de l'indice de qualification de la variable de requête.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération de l'ordre d'affichage.
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Récupération d'attribut de champ requis.
     *
     * @param string|null $key Clé d'indexe d'attributs. Syntaxe à point permise.
     *                         Retour la liste complète si null (défaut).
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getRequired(?string $key = null, $default = null);

    /**
     * Récupération de l'identifiant de qualification.
     *
     * @return string
     */
    public function getSlug(): string;

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Récupération du type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Récupération de la valeur.
     *
     * @param boolean Activation de la valeur de retour au format brut.
     *
     * @return mixed
     */
    public function getValue(bool $raw = true);

    /**
     * Récupération de la liste des valeurs.
     *
     * @param bool $raw Activation de la valeur de retour au format brut.
     * @param string|null $glue Caractère(s) d'assemblage de la valeur.
     *
     * @return string|array
     */
    public function getValues(bool $raw = true, ?string $glue = ', ');

    /**
     * Vérification d'existance d'une étiquette.
     *
     * @return boolean
     */
    public function hasLabel(): bool;

    /**
     * Vérification d'existance d'encapsuleur HTML.
     *
     * @return boolean
     */
    public function hasWrapper(): bool;

    /**
     * Vérification si le champ retourne des erreurs de traitement.
     *
     * @return boolean
     */
    public function onError(): bool;

    /**
     * Traitement récursif des tests de validation.
     *
     * @param string|array $validations Test de validation à traiter.
     * @param array $results Liste des tests de validations traités.
     *
     * @return array
     */
    public function parseValidations($validations, array $results = []): array;

    /**
     * Initialisation (préparation) du champ.
     *
     * @return static
     */
    public function prepare(): FactoryField;

    /**
     * Réinitionalisation  de la valeur.
     *
     * @return static
     */
    public function resetValue(): FactoryField;

    /**
     * Rendu de l'affichage.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Préparation du rendu de l'affichage.
     *
     * @return static
     */
    public function renderPrepare(): FactoryField;

    /**
     * Définition d'une attributs de configuration complémentaire.
     *
     * @param string $key Clé d'indexe de l'attribut.
     * @param mixed $value Valeur à définir.
     *
     * @return static
     */
    public function setExtra(string $key, $value): FactoryField;

    /**
     * Définition du statut de l'indicateur de champ en erreur.
     *
     * @param boolean $status
     *
     * @return static
     */
    public function setOnError(bool $status = true): FactoryField;

    /**
     * Définition de l'ordre d'affichage.
     *
     * @param int $position Valeur de la position.
     *
     * @return $this
     */
    public function setPosition(int $position = 0): FactoryField;

    /**
     * Définition de la valeur du champ basée sur la donnée en correspondance stockée en session.
     *
     * @return static
     */
    public function setSessionValue(): FactoryField;

    /**
     * Définition de la valeur du champ.
     *
     * @param mixed $value Valeur à définir.
     *
     * @return static
     */
    public function setValue($value): FactoryField;

    /**
     * Vérification d'une propriété|récupération de la liste des proriétés de support .
     *
     * @param string|null $support Propriété du support à vérifier.
     *
     * @return array|boolean
     */
    public function supports(?string $support = null);
}