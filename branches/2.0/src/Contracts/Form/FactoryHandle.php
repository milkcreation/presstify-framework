<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

use tiFy\Http\RedirectResponse;
use tiFy\Contracts\Support\ParamsBag;

interface FactoryHandle extends FactoryResolver, ParamsBag
{
    /**
     * Traitement de l'échec de la requête de soumission du formulaire.
     *
     * @return static
     */
    public function fail(): FactoryHandle;

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
     * Préparation des données de traitement de la requête.
     *
     * @return static
     */
    public function prepare(): FactoryHandle;

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
    public function setRedirectUrl(string $url, bool $raw = false): FactoryHandle;

    /**
     * Traitement du succès de la requête de soumission du formulaire.
     *
     * @return static
     */
    public function success(): FactoryHandle;

    /**
     * Traitement de la validation de soumission du formulaire.
     *
     * @return static
     */
    public function validate(): FactoryHandle;

    /**
     * Vérification de l'origine de la requête.
     *
     * @return boolean
     */
    public function verify(): bool;
}