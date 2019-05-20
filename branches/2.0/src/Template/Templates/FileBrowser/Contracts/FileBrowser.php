<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileBrowser\Contracts;

use League\Flysystem\AdapterInterface;
use tiFy\Contracts\Filesystem\Filesystem;
use tiFy\Contracts\Template\{TemplateFactory as TemplateFactoryContract};
use tiFy\Contracts\Template\TemplateFactory;

interface FileBrowser extends TemplateFactory
{
    /**
     * Récupération de l'instance du gestionnaire de fichiers en contexte.
     *
     * @return AdapterInterface
     */
    public function adapter(): AdapterInterface;

    /**
     * Récupération de l'instance du controleur de traitment des requête Ajax (XHR).
     *
     * @return Ajax
     */
    public function ajax(): Ajax;

    /**
     * Récupération de l'instance du controleur de fil d'ariane.
     *
     * @return Breadcrumb
     */
    public function breadcrumb(): Breadcrumb;

    /**
     * Récupération de l'instance du gestionnaire de fichiers.
     *
     * @return Filesystem
     */
    public function filesystem(): Filesystem;

    /**
     * Récupération d'un instance de fichier.
     *
     * @param string|null $path Chemin relatif vers le fichier. Si null, utilise le chemin relatif courant.
     *
     * @return Fileinfo|null
     */
    public function getFile(?string $path = null): ?Fileinfo;

    /**
     * Récupération de la liste des fichiers associée à un chemin relatif.
     *
     * @param string|null $path Chemin relatif vers le répertoire. Si null, utilise le chemin relatif courant.
     * @param boolean $recursive Activation du traitement récursif. (non recommandé).
     *
     * @return FileCollection|FileInfo[]|null
     */
    public function getFiles(?string $path = null, bool $recursive = false): ?FileCollection;

    /**
     * Récupération d'un icône.
     *
     * @return string|null
     */
    public function getIcon($name, ...$args): ?string;

    /**
     * Récupération du chemin relatif courant.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Récupération d'un instance du gestionnaire d'icones.
     *
     * @return IconSet
     */
    public function icon(): IconSet;

    /**
     * {@inheritdoc}
     *
     * @return FileBrowser
     */
    public function prepare(): TemplateFactoryContract;

    /**
     * Définition du chemin relatif courant.
     *
     * @return FileBrowser
     */
    public function setPath(string $path): FileBrowser;

    /**
     * Instance de la barre latérale de contrôle.
     *
     * @return Sidebar
     */
    public function sidebar(): Sidebar;
}