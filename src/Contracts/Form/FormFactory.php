<?php

declare(strict_types=1);

namespace tiFy\Contracts\Form;

use Pollen\Http\RequestInterface;
use Pollen\Support\Proxy\HttpRequestProxyInterface;
use tiFy\Contracts\View\Engine as ViewEngine;

/**
 * @mixin \tiFy\Form\Concerns\FactoryBagTrait
 * @mixin \tiFy\Support\Concerns\LabelsBagTrait
 * @mixin \tiFy\Support\Concerns\MessagesBagTrait
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 */
interface FormFactory extends HttpRequestProxyInterface
{
    /**
     * Résolution de sortie de l'affichage du formulaire.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Chargement.
     *
     * @return FormFactory
     */
    public function boot(): FormFactory;

    /**
     * Initialisation.
     *
     * @return FormFactory
     */
    public function build(): FormFactory;

    /**
     * Récupération de la chaîne de sécurisation du formulaire (CSRF).
     *
     * @return string
     */
    public function csrf(): string;

    /**
     * Déclaration d'un message d'erreur.
     *
     * @param string $message Intitulé du message.
     * @param array $datas Données associées à l'erreur
     *
     * @return string Identifiant de qualification du message d'erreur
     */
    public function error(string $message, array $datas = []): string;

    /**
     * Récupération de l'instance du gestionnaire de formulaire.
     *
     * @return FormManager|null
     */
    public function formManager(): ?FormManager;

    /**
     * Récupération de l'action du formulaire (url).
     *
     * @return string
     */
    public function getAction(): string;

    /**
     * Récupération de l'alias de qualification du champ.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Récupération de l'ancre du formulaire.
     *
     * @return string
     */
    public function getAnchor(): string;

    /**
     * Récupération de l'indice du formulaire.
     *
     * @return int
     */
    public function getIndex(): int;

    /**
     * Récupération de la méthode de soumission du formulaire.
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * Récupération de la liste des attributs de support.
     *
     * @return string[]
     */
    public function getSupports(): array;

    /**
     * Récupération de l'intitulé de qualification du formulaire.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Vérification du status en erreur du formulaire.
     *
     * @return bool
     */
    public function hasError(): bool;

    /**
     * Vérification de l'indicateur de chargement.
     *
     * @return bool
     */
    public function isBooted(): bool;

    /**
     * Vérification de l'indicateur d'initialisation.
     *
     * @return bool
     */
    public function isBuilt(): bool;

    /**
     * Vérification de soumission du formulaire.
     *
     * @return bool
     */
    public function isSubmitted(): bool;

    /**
     * Vérifie si le formulaire a été soumis avec succès.
     *
     * @return bool
     */
    public function isSuccessed(): bool;

    /**
     * Evénement de déclenchement à l'initialisation du formulaire en tant que formulaire courant.
     *
     * @return void
     */
    public function onSetCurrent(): void;

    /**
     * Evénement de déclenchement à la réinitialisation du formulaire courant du formulaire.
     *
     * @return void
     */
    public function onResetCurrent(): void;

    /**
     * Affichage.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Initialisation du rendu.
     *
     * @return static
     */
    public function renderBuild(): FormFactory;

    /**
     * Initialisation du rendu des attributrs HTML.
     *
     * @return static
     */
    public function renderBuildAttrs(): FormFactory;

    /**
     * Initialisation du rendu de l'identifiant de qualification HTML.
     *
     * @return static
     */
    public function renderBuildId(): FormFactory;

    /**
     * Initialisation du rendu des messages de notification.
     *
     * @return static
     */
    public function renderBuildNotices(): FormFactory;

    /**
     * Initialisation du rendu de l'encapsulation.
     *
     * @return static
     */
    public function renderBuildWrapper(): FormFactory;

    /**
     * Définition de l'alias de qualification.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): FormFactory;

    /**
     * Définition du gestionnaire de formulaire.
     *
     * @param FormManager $formManager
     *
     * @return static
     */
    public function setFormManager(FormManager $formManager): FormFactory;

    /**
     * Définition de l'indicateur de statut de formulaire en succès.
     *
     * @param boolean $status
     *
     * @return static
     */
    public function setSuccessed(bool $status = true): FormFactory;

    /**
     * Définition de la requête de traitement du formulaire.
     *
     * @param RequestInterface $request
     *
     * @return static
     */
    public function setHandleRequest(RequestInterface $request): FormFactory;

    /**
     * Vérification de support.
     *
     * @param string $support
     *
     * @return array|bool
     */
    public function supports(string $support);

    /**
     * Récupération du nom de qualification du formulaire dans les attributs de balises HTML.
     *
     * @return string
     */
    public function tagName(): string;

    /**
     * Instance du gestionnaire de gabarits d'affichage ou rendu du gabarit d'affichage.
     *
     * @param string|null view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewEngine|string
     */
    public function view(?string $view = null, array $data = []);
}