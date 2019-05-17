<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileBrowser\Contracts;

use BadMethodCallException;
use SplFileInfo;
use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\Template\FactoryAwareTrait;

/**
 * @mixin SplFileInfo
 */
interface FileInfo extends ParamsBag, FactoryAwareTrait
{
    /**
     * Délégation d'appel des méthodes de la classe SplFileInfo.
     * {@internal L'environnement du fichier doit être local}
     *
     * @param string $name Nom de qualification de la méthode.
     * @param array $arguments Liste des variables passés en arguments à la méthode.
     *
     * @throws BadMethodCallException
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments);

    /**
     * Résolution de sortie de la classe sous la forme d'une chaine de caractère.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getBasename(): string;

    /**
     * Récupération du chemin relatif vers le répertoire parent.
     *
     * @return string
     */
    public function getDirname(): string;

    /**
     * Récupération de l'extension.
     *
     * @return string
     */
    public function getExtension(): string;

    /**
     * Récupération de la date dans un format lisible.
     *
     * @param string $format Format d'affichage de la date.
     *
     * @return string|null
     */
    public function getHumanDate(string $format = 'Y-m-d'): ?string;

    /**
     * Récupération de la taille au format lisible.
     *
     * @param int $decimals Nombre de décimales attendues.
     *
     * @return string|null
     */
    public function getHumanSize(int $decimals = 2): ?string;

    /**
     * Récupération du type au format lisible.
     *
     * @return string|null
     */
    public function getHumanType(): ?string;

    /**
     * Récupération de l'icône représentative.
     *
     * @return FileIcon
     */
    public function getIcon(): FileIcon;

    /**
     * Récupération du type mime du fichier.
     *
     * @see https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
     * @see https://developer.mozilla.org/fr/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Complete_list_of_MIME_types
     *
     * @return string|null
     */
    public function getMimetype(): ?string;

    /**
     * Récupération du chemin absolu vers le fichier.
     *
     * @return string
     */
    public function getPathname(): string;

    /**
     * Récupération du chemin relatif.
     *
     * @return string
     */
    public function getRelPath(): string;

    /**
     * Récupération de la taille.
     *
     * @return float
     */
    public function getSize(): float;

    /**
     * Récupération de la date de création au format Unix.
     *
     * @return int
     */
    public function getTimestamp(): int;

    /**
     * Récupération du type de fichier.
     *
     * @return string dir|file|link
     */
    public function getType(): string;

    /**
     * Vérifie s'il s'agit d'un dossier.
     *
     * @return boolean
     */
    public function isDir(): bool;

    /**
     * Vérifie s'il s'agit d'un fichier.
     *
     * @return boolean
     */
    public function isFile(): bool;

    /**
     * Vérifie s'il s'agit d'un lien symbolique.
     *
     * @return boolean
     */
    public function isLink(): bool;

    /**
     * Vérifie s'il s'agit d'un fichier local.
     *
     * @return boolean
     */
    public function isLocal(): bool;

    /**
     * Vérifie s'il s'agit d'un répertoire racine.
     *
     * @return boolean
     */
    public function isRoot(): bool;
}