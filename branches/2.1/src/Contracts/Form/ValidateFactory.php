<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

/**
 * @mixin \tiFy\Form\Concerns\FormAwareTrait
 */
interface ValidateFactory
{
    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): ValidateFactory;

    /**
     * Appel d'un test d'intégrité de valeur.
     *
     * @param string|callable $callback Fonction de traitement de vérification.
     * @param mixed $value Valeur à vérifier.
     * @param array $args Liste des variables passées en argument.
     *
     * @return boolean
     */
    public function call($callback, $value, $args = []): bool;

    /**
     * Méthode de controle par défaut.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return boolean
     */
    public function __return_true($value): bool;

    /**
     * Compare deux chaînes de caractères.
     * @internal ex. mot de passe <> confirmation mot de passe
     *
     * @param mixed $value Valeur du champ courant à comparer.
     * @param mixed $tags Variables de qualification de champs de comparaison.
     * @param boolean $raw Récupération du format brut du champ de comparaison.
     *
     * @return boolean
     */
    public function compare($value, $tags, $raw = true): bool;
}