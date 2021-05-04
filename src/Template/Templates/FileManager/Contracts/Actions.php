<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileManager\Contracts;

use Exception;
use Pollen\Http\JsonResponseInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Contracts\Template\FactoryActions;

interface Actions extends FactoryActions
{
    /**
     * Parcours d'un dossier.
     *
     * @return JsonResponseInterface
     *
     * @throws Exception
     */
    public function doBrowse(): JsonResponseInterface;

    /**
     * Création de dossier.
     *
     * @return array
     *
     * @throws Exception
     */
    public function doCreate(): JsonResponseInterface;

    /**
     * Suppression d'un élément (fichier ou dossier).
     *
     * @return array
     *
     * @throws Exception
     */
    public function doDelete(): JsonResponseInterface;

    /**
     * Téléchargement de fichier.
     *
     * @return StreamedResponse|null
     */
    public function doDownload(): ?StreamedResponse;

    /**
     * Récupération d'un élément (fichier ou dossier).
     *
     * @return JsonResponseInterface
     *
     * @throws Exception
     */
    public function doFetch(): JsonResponseInterface;

    /**
     * Renommage d'un élément (fichier ou dossier).
     *
     * @return JsonResponseInterface
     *
     * @throws Exception
     */
    public function doRename(): JsonResponseInterface;

    /**
     * Téléversement de fichiers.
     *
     * @return JsonResponseInterface
     *
     * @throws Exception
     */
    public function doUpload(): JsonResponseInterface;

    /**
     * Message de notification.
     * @see \Pollen\Partial\Drivers\NoticeDriver
     *
     * @param string $message Message de notification
     * @param string $type Type de message. error|info|success|warning.
     * @param array $attrs Liste des attributs de personnalisation.
     *
     * @return string
     */
    public function notice(string $message, string $type = 'info', array $attrs = []): string;
}