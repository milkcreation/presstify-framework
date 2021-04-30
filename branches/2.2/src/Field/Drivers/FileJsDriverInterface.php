<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriverInterface;

interface FileJsDriverInterface extends FieldDriverInterface
{
    /**
     * Traitement des options du moteur de téléchargement Dropzone.
     * @see https://www.dropzonejs.com/#configuration
     *
     * @return static
     */
    public function parseDropzone(): FileJsDriverInterface;
}