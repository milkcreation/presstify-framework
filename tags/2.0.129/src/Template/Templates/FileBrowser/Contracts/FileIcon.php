<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileBrowser\Contracts;

use tiFy\Contracts\Template\FactoryAwareTrait;

interface FileIcon extends FactoryAwareTrait
{
    /**
     * Résolution de sortie d'une instance de la classe sous forme de chaine de caractères.
     *
     * @return string
     */
    public function __toString() : string;

    /**
     * Vérification d'existance du fichier associé.
     *
     * @return boolean
     */
    public function hasFile(): bool;

    /**
     * Récupération de l'icône associé au fichier.
     *
     * @return string
     */
    public function get(): string;

    /**
     * Définition du fichier associé.
     *
     * @param FileInfo $file
     *
     * @return static
     */
    public function set(FileInfo $file): FileIcon;
}