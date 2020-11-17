<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileManager;

use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Support\Proxy\Partial;
use tiFy\Template\Factory\Actions as BaseActions;
use tiFy\Template\Templates\FileManager\Contracts\Factory;
use tiFy\Template\Templates\FileManager\Contracts\Actions as ActionsContract;

class Actions extends BaseActions implements ActionsContract
{
    /**
     * Instance du gabarit associé.
     * @var Factory
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function doBrowse(): array
    {
        $path = $this->factory->request()->input('path');
        $this->factory->setPath($path);

        return [
            'success' => true,
            'views'   => [
                'files' => (string)$this->factory->viewer(
                    'browser-items', ['files' => $this->factory->getFiles()]
                )
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function doCreate(): array
    {
        $path = $this->factory->request()->input('path');
        $file = $this->factory->getFile($path);
        $name = request()->input('name');

        if (!validator()::notEmpty()->validate($name)) {
            return [
                'success' => false,
                'views'   => [
                    'notice' => (string)$this->notice(__('Le nom du dossier ne peut être vide.', 'tify'), 'warning')
                ]
            ];
        }

        $root = $file->isDir() ? $path : $file->getDirname();
        $path = "{$root}/{$name}";

        if ($this->factory->filesystem()->has($path)) {
            return [
                'success' => false,
                'views'   => [
                    'notice' => (string)$this->notice(
                        __('Un dossier portant ce nom autre existe déjà dans le répertoire courant.', 'tify'),
                        'warning'
                    )
                ]
            ];
        } elseif ($this->factory->filesystem()->createDir($path)) {
            $this->factory->setPath($root);

            return [
                'success' => true,
                'views'   => [
                    'breadcrumb' => (string)$this->factory->breadcrumb(),
                    'content'    => (string)$this->factory->getFiles(),
                    'sidebar'    => (string)$this->factory->sidebar(),
                    'notice'     => (string)$this->notice(__('Le dossier a été créé avec succès.', 'tify'), 'success')
                ]
            ];
        } else {
            return [
                'success' => false,
                'views'   => [
                    'notice' => (string)$this->notice(
                        __('ERREUR SYSTEME : Impossible de créer le dossier.', 'tify'), 'error')
                ]
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function doDelete(): array
    {
        $path = $this->factory->request()->input('path');

        if (!$this->factory->filesystem()->has($path)) {
            return [
                'success' => false,
                'views'   => [
                    'notice' => $this->notice(__('Impossible de trouver l\'élément à supprimer.', 'tify'), 'warning')
                ]
            ];
        } else {
            $this->factory->setPath($path);
            $file = $this->factory->getFile($path);

            if ($file->isDir()) {
                $this->factory->adapter()->deleteDir($path);
            } else {
                $this->factory->adapter()->delete($path);
            }

            $this->factory->setPath($file->getDirname());

            return [
                'success' => true,
                'views'   => [
                    'breadcrumb' => (string)$this->factory->breadcrumb(),
                    'content'    => (string)$this->factory->getFiles(),
                    'sidebar'    => (string)$this->factory->sidebar(),
                    'notice'     => $this->notice(__('L\'élément a été supprimé avec succès.', 'tify'), 'success')
                ]
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function doDownload(): ?StreamedResponse
    {
        $path = $this->factory->request()->input('path');

        try {
            return $this->factory->filesystem()->download($path);
        } catch (FileNotFoundException $e) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function doFetch(): array
    {
        $path = $this->factory->request()->input('path');

        if ($path && ($path !== '/') && !$this->factory->filesystem()->has($path)) {
            return [
                'success' => false,
                'views'   => [
                    'notice' => $this->notice(__('Impossible de trouver l\'élément.' . $path, 'tify'), 'warning')
                ]
            ];
        } else {
            $this->factory->setPath($path);
            $file = $this->factory->getFile($path);

            if ($file->isDir()) {
                return [
                    'success' => true,
                    'views'   => [
                        'breadcrumb' => (string)$this->factory->breadcrumb(),
                        'content'    => (string)$this->factory->getFiles(),
                        'sidebar'    => (string)$this->factory->sidebar()
                    ]
                ];
            } else {
                return [
                    'success' => true,
                    'views'   => [
                        'sidebar' => (string)$this->factory->sidebar()
                    ]
                ];
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function doPreview(): ?StreamedResponse
    {
        $path = $this->factory->request()->input('path');

        try {
            return $this->factory->filesystem()->response($path);
        } catch (FileNotFoundException $e) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function doRename(): array
    {
        $path = $this->factory->request()->input('path');
        $name = $this->factory->request()->input('name');

        if (!$this->factory->filesystem()->has($path)) {
            return [
                'success' => false,
                'views'   => [
                    'notice' => $this->notice(__('Impossible de trouver l\'élément à renommer.', 'tify'), 'warning')
                ]
            ];
        } elseif (!validator()::notEmpty()->validate($name)) {
            return [
                'success' => false,
                'views'   => [
                    'notice'    => $this->notice(__('Le nom ne peut être vide.'), 'warning')
                ]
            ];
        }

        $this->factory->setPath($path);
        $file = $this->factory->getFile($path);
        $newpath = $this->factory->getFile()->getDirname() . '/' . $name;
        $newpath .= ($file->isFile() && (request()->input('keep') === 'on') && $file->getExtension())
            ? '.' . $file->getExtension()
            : '';

        if ($this->factory->filesystem()->has($newpath)) {
            return [
                'success' => false,
                'views'   => [
                    'notice'    => $this->notice(__('Un autre élément porte déjà ce nom.', 'tify'), 'warning')
                ]
            ];
        } elseif ($this->factory->adapter()->rename($path, $newpath)) {
            $this->factory->setPath($newpath);

            return [
                'success' => true,
                'views'   => [
                    'breadcrumb' => (string)$this->factory->breadcrumb(),
                    'content'    => (string)$this->factory->getFiles(),
                    'sidebar'    => (string)$this->factory->sidebar(),
                    'notice'    => $this->notice(__('L\'élément a bien été renommé.', 'tify'), 'success')
                ]
            ];
        } else {
            return [
                'success' => false,
                'views'   => [
                    'notice' => (string)$this->notice(
                        __('ERREUR SYSTEME : Impossible de renommer l\'élément.', 'tify'), 'error')
                ]
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function doUpload(): array
    {
        $path = $this->factory->request()->input('path');
        $this->factory->setPath($path);
        $file = $this->factory->getFile($path);

        $path = $file->isDir() ? $path : $file->getDirname();

        foreach (request()->files as $key => $f) {
            /* @var UploadedFile $f */
            $this->factory->filesystem()->put(
                $path . '/' . $f->getClientOriginalName(),
                file_get_contents($f->getPathname())
            );
        }

        return [
            'success' => true,
            'views'   => [
                'breadcrumb' => (string)$this->factory->breadcrumb(),
                'content'    => (string)$this->factory->getFiles()
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function notice($message, $type = 'info', $attrs = []): string
    {
        return Partial::get('notice', array_merge([
            'attrs'   => [
                'class' => 'FileManager-noticeMessage FileManager-noticeMessage--'. $type
            ],
            'content' => $message,
            'dismiss' => true,
            'timeout' => 2000,
            'type'    => $type
        ], $attrs))->render();
    }
}