<?php

declare(strict_types=1);

namespace tiFy\Contracts\Form;

use Pollen\Http\RedirectResponse;

/**
 * @mixin \tiFy\Form\Concerns\FormAwareTrait
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 */
interface HandleFactory
{
    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): HandleFactory;

    /**
     * Traitement de l'échec de la requête de soumission du formulaire.
     *
     * @return static
     */
    public function fail(): HandleFactory;

    /**
     * Récupération de l'url de redirection.
     *
     * @return string
     */
    public function getRedirectUrl(): string;

    /**
     * Récupération de la valeur de la protection CSRF.
     *
     * @return string
     */
    public function getToken(): string;

    /**
     * Vérification de soumission du formulaire.
     *
     * @return boolean
     */
    public function isSubmitted(): bool;

    /**
     * Vérification du succes de validation de la soumission du formulaire.
     *
     * @return bool
     */
    public function isValidated(): bool;

    /**
     * Traitement de la requête de soumission du formulaire.
     *
     * @return RedirectResponse|null
     */
    public function response(): ?RedirectResponse;

    /**
     * Redirection de la requête de traitement du formulaire.
     *
     * @return RedirectResponse
     */
    public function redirect(): RedirectResponse;

    /**
     * Définition de l'url de redirection.
     *
     * @param string $url
     * @param bool $raw Désactivation du formatage (indicateur de succès && ancre).
     *
     * @return static
     */
    public function setRedirectUrl(string $url, bool $raw = false): HandleFactory;

    /**
     * Traitement du succès de la requête de soumission du formulaire.
     *
     * @return static
     */
    public function success(): HandleFactory;

    /**
     * Traitement de la validation de soumission du formulaire.
     *
     * @return static
     */
    public function validate(): HandleFactory;
}