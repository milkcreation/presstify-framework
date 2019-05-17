<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileBrowser;

use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\FileBrowser\Contracts\{FileBrowser, FileIcon as FileIconContract, FileInfo};

class FileIcon implements FileIconContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit d'affichage.
     * @var FileBrowser
     */
    protected $factory;

    /**
     * Instance du fichier associé.
     * @var FileInfo
     */
    protected $file;

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->get();
    }

    /**
     * Vérification d'existance du fichier associé.
     *
     * @return boolean
     */
    public function hasFile(): bool
    {
        return $this->file instanceof FileInfo;
    }

    /**
     * {@inheritDoc}
     *
     * @see https://developer.mozilla.org/fr/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Complete_list_of_MIME_types
     */
    public function get(): string
    {
        $class = '';
        if ($this->hasFile()) {
            switch ($mime = $this->file->getMimetype()) {
                case 'application/pdf' :
                    $class = 'fa fa-file-pdf-o';
                    break;
                case 'application/msword' :
                case 'application/vnd.ms-word' :
                case 'application/vnd.oasis.opendocument.text':
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml' :
                    $class = 'fa fa-file-word-o';
                    break;
                case 'application/vnd.ms-excel' :
                case 'application/vnd.openxmlformats-officedocument.spreadsheetml' :
                case 'application/vnd.oasis.opendocument.spreadsheet' :
                    $class = 'fa fa-file-excel-o';
                    break;
                case 'application/vnd.ms-powerpoint' :
                case 'application/vnd.openxmlformats-officedocument.presentationml' :
                case 'application/vnd.oasis.opendocument.presentation' :
                    $class = 'fa fa-file-powerpoint-o';
                    break;
                case 'text/plain' :
                    $class = 'fa fa-file-text-o';
                    break;
                case 'text/html' :
                case 'text/x-php' :
                case 'application/json' :
                    $class = 'fa fa-file-code-o';
                    break;
                case 'application/gzip' :
                case 'application/zip' :
                    $class = 'fa fa-file-archive-o';
                    break;
            }

            switch($type = preg_replace('#(\/[a-zA-Z-\.\+]+)$#', '', $mime)) {
                case 'audio' :
                    $class = 'fa-file-audio-o';
                    break;
                case 'image' :
                    $class = 'fa fa-file-image-o';
                    break;
                case 'directory' :
                    $class = 'fa fa-folder';
                    break;
                case 'video' :
                    $class = 'fa fa-file-video-o';
                    break;
            }

            if (!$class) {
                $class = 'fa fa-file';
            }
        }

        return (string)partial('tag', [
            'tag'   => 'span',
            'attrs' => [
                'class' => 'Browser-fileIcon' . ($class ? ' ' . $class : '')
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function set(FileInfo $file): FileIconContract
    {
        $this->file = $file;

        return $this;
    }
}