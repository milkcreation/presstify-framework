<?php declare(strict_types=1);

namespace tiFy\Contracts\Partial;

interface Modal extends PartialFactory
{
    /**
     * Récupération de l'url de traitement Xhr.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Définition de l'url de traitement Xhr.
     *
     * @param string|null $url
     *
     * @return $this
     */
    public function setUrl(?string $url = null): Modal;

    /**
     * Affichage d'un lien de déclenchement de la modale.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return string
     */
    public function trigger(array $attrs = []);

    /**
     * Chargement du contenu de la modale via une requête XHR.
     *
     * @return void
     */
    public function xhrResponse();
}