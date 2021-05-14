<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Partial\Drivers;

use Exception;
use Pollen\Partial\Drivers\DownloaderDriver as BaseDownloaderDriver;
use Pollen\Support\ParamsBag;
use Pollen\Support\Proxy\EncrypterProxy;
use Pollen\Validation\Validator as v;
use tiFy\Proxy\Url;
use tiFy\Support\MimeTypes;

class DownloaderDriver extends BaseDownloaderDriver
{
    use EncrypterProxy;

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function getFilename(...$args): string
    {
        if ($decrypt = $this->decrypt((string)$args[0])) {
            $var = (new ParamsBag())->set(json_decode(base64_decode($decrypt), true));
        } else {
            throw new Exception(
                __('ERREUR SYSTEME : Impossible de récupérer les données de téléchargement du fichier.', 'tify')
            );
        }

        $src = $var->get('src');
        if (is_numeric($src)) {
            $path = get_attached_file($src);
        } elseif (!is_string($src)) {
            throw new Exception(
                __('Téléchargement impossible, la fichier source n\'est pas valide.', 'tify')
            );
        } elseif (v::url()->validate(dirname($src))) {
            $path = Url::rel($src);
        } else {
            $path = $src;
        }

        if (file_exists($path)) {
            $filename = $path;
        } elseif (file_exists($var->get('basedir') . $path)) {
            $filename = $var->get('basedir') . $path;
        } else {
            throw new Exception(
                __('Téléchargement impossible, le fichier n\'est pas disponible.', 'tify')
            );
        }

        $types = $var->get('types');
        if (is_string($var->get('types'))) {
            $types = array_map('trim', explode(',', $var->get('types')));
        }

        if (!MimeTypes::inAllowedType($filename, $types)) {
            throw new Exception(
                __('Téléchargement impossible, ce type de fichier n\'est pas autorisé.', 'tify')
            );
        }

        return $filename;
    }
}