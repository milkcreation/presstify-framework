<?php declare(strict_types=1);

namespace tiFy\Wordpress\Template\Templates\FileBrowser;

use tiFy\Template\Templates\FileBrowser\Contracts\{FileInfo};
use tiFy\Template\Templates\FileBrowser\IconSet as tiFyTmplBrowserIconSet;

class IconSet extends tiFyTmplBrowserIconSet
{
    /**
     * @inheritDoc
     */
    public function file(FileInfo $file): string
    {
        if ($file->isDir()) {
            $class = 'dashicons dashicons-category';
        } else {
            switch ($type = wp_ext2type($file->getExtension())) {
                case 'archive' :
                case 'audio' :
                case 'code' :
                case 'document' :
                case 'interactive' :
                case 'spreadsheet' :
                case 'text' :
                case 'video' :
                    $class = "dashicons dashicons-media-{$type}";
                    break;
                case 'image' :
                    $class = 'dashicons dashicons-format-image';
                    break;
                default :
                    $class = 'dashicons dashicons-media-default';
                    break;
            }
        }

        return (string)partial('tag', [
            'tag'   => 'span',
            'attrs' => [
                'class' => 'Browser-icon Browser-icon--file' . ($class ? ' ' . $class : '')
            ]
        ]);
    }
}