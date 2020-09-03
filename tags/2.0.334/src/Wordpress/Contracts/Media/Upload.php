<?php declare(strict_types=1);

namespace tiFy\Wordpress\Contracts\Media;

use Exception;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Support\ParamsBag;

interface Upload
{
    /**
     * Ajout d'un média.
     *
     * @param string $file Chemin absolu vers le fichier.
     * @param array $args Liste des paramètres d'import du fichier.
     *
     * @return int
     *
     * @throws Exception
     */
    public function add(string $file, array $args = []): int;

    /**
     * Liste des paramètres de configuration par défaut.
     *
     * @return array
     */
    public function defaultParams(): array;

    /**
     * Récupération de la taille maximum permise par fichier.
     *
     * @return int
     */
    public function getMaxSize(): int;

    /**
     * Récupération du chemin absolu vers le répertoire de destination.
     *
     * @return string|null
     */
    public function getStorageDir(): ?string;

    /**
     * Vérifie l'activation de forçage du renouvellement d'un fichier déjà existant.
     *
     * @return bool
     */
    public function isRenewable(): bool;

    /**
     * Définition|Récupération|Instance des paramètres de configuration.
     *
     * @param string|null $key Clé d'indice du paramètre|Liste des définition. Retourne l'instance si null.
     * @param string|null $default Valeur de retour par défaut de récupération d'un paramètre unique.
     *
     * @return string|array|ParamsBag
     */
    public function params($key = null, $default = null);

    /**
     * Définition de la taille maximum autorisé par fichier.
     *
     * @param int $size
     *
     * @return static
     */
    public function setMaxSize(int $size): Upload;

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $params
     *
     * @return static
     */
    public function setParams(array $params): Upload;

    /**
     * Définition du chemin absolu vers le répertoire de destination.
     *
     * @param string $dir
     *
     * @return static
     */
    public function setStorageDir(string $dir): Upload;

    /**
     * Définition d'activation de forçage du renouvellement d'un fichier déjà existant.
     *
     * @param bool $renew
     *
     * @return static
     */
    public function setRenewable(bool $renew): Upload;

    /**
     * Instance de traitement du répertoire de destination.
     *
     * @return LocalFilesystem|null
     */
    public function storage(): ?LocalFilesystem;
}