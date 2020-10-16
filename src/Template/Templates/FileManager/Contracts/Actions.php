<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileManager\Contracts;

use Exception;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Contracts\Template\FactoryActions;

interface Actions extends FactoryActions
{
    /**
     * Parcours d'un dossier.
     *
     * @return array
     *
     * @throws Exception
     */
    public function doBrowse(): array;

    /**
     * Création de dossier.
     *
     * @return array
     *
     * @throws Exception
     */
    public function doCreate(): array;

    /**
     * Suppression d'un élément (fichier ou dossier).
     *
     * @return array
     *
     * @throws Exception
     */
    public function doDelete(): array;

    /**
     * Téléchargement de fichier.
     *
     * @return StreamedResponse|null
     */
    public function doDownload(): ?StreamedResponse;

    /**
     * Récupération d'un élément (fichier ou dossier).
     *
     * @return array
     *
     * @throws Exception
     */
    public function doFetch(): array;

    /**
     * Renommage d'un élément (fichier ou dossier).
     *
     * @return array
     *
     * @throws Exception
     */
    public function doRename(): array;

    /**
     * Téléversement de fichiers.
     *
     * @return array
     *
     * @throws Exception
     */
    public function doUpload(): array;

    /**
     * Message de notification.
     * @see \tiFy\Partial\Driver\Notice\Notice
     *
     * @param string $message Message de notification
     * @param string $type Type de message. error|info|success|warning.
     * @param array $attrs Liste des attributs de personnalisation.
     *
     * @return string
     */
    public function notice(string $message, string $type = 'info', array $attrs = []): string;
}