<?php declare(strict_types=1);

namespace tiFy\Contracts\Field;

interface FileJs extends FieldFactory
{
    /**
     * Récupération de l'url de traitement.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Traitement des options du moteur de téléchargement Dropzone.
     * @see https://www.dropzonejs.com/#configuration
     *
     * @return $this
     */
    public function parseDropzone(): FieldFactory;

    /**
     * Définition de la route de traitement.
     *
     * @return $this
     */
    public function prepareRoute(): FieldFactory;

    /**
     * Génération de la réponse HTTP via une requête XHR.
     *
     * @return array
     */
    public function xhrResponse(): array;
}