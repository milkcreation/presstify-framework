<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileBrowser;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\FileBrowser\Contracts\{Ajax as AjaxContract, FileBrowser};
use tiFy\Support\ParamsBag;

class Ajax extends ParamsBag implements AjaxContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit associé.
     * @var FileBrowser
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function defaults()
    {
        return [
            'url'      => $this->getFactory()->baseUrl() . '/xhr',
            'dataType' => 'json',
            'type'     => 'POST'
        ];
    }

    /**
     * @inheritDoc
     */
    public function parse()
    {
        parent::parse();

        $this->getFactory()->param()->set('attrs.data-options.ajax', $this->all());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function handler(...$args)
    {
        $this->factory->setPath($path = request()->input('path'));
        $file = $this->factory->getFile($path);

        switch (request()->input('action')) {
            case 'browse' :
                return [
                    'success' => true,
                    'views'   => [
                        'files'    => (string)$this->factory->viewer(
                            'explorer-items', ['files' => $this->factory->getFiles()]
                        )
                    ]
                ];
                break;
            case 'create' :
                $path = $file->isDir() ? $path : $file->getDirname();
                $this->factory->filesystem()->createDir($path . '/' . request()->input('name'));

                return [
                    'success' => true,
                    'views'   => [
                        'breadcrumb' => (string)$this->factory->breadcrumb(),
                        'content'    => (string)$this->factory->getFiles(),
                        'sidebar'    => (string)$this->factory->sidebar()
                    ]
                ];
                break;
            case 'delete' :
                if ($file->isDir()) {
                    $this->factory->adapter()->deleteDir($file->getRelPath());
                } else {
                    $this->factory->adapter()->delete($file->getRelPath());
                }

                $this->factory->setPath($file->getDirname());

                return [
                    'success' => true,
                    'views'   => [
                        'breadcrumb' => (string)$this->factory->breadcrumb(),
                        'content'    => (string)$this->factory->getFiles(),
                        'sidebar'    => (string)$this->factory->sidebar()
                    ]
                ];
                break;
            case 'get' :
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
                break;
            case 'rename' :
                $newpath = $this->factory->getFile()->getDirname() . '/' . request()->input('name');
                $newpath .= ($file->isFile() && (request()->input('keep') === 'on') && $file->getExtension())
                    ? '.' . $file->getExtension()
                    : '';

                $this->factory->adapter()->rename($path, $newpath);

                $this->factory->setPath($newpath);

                return [
                    'success' => true,
                    'views'   => [
                        'breadcrumb' => (string)$this->factory->breadcrumb(),
                        'content'    => (string)$this->factory->getFiles(),
                        'sidebar'    => (string)$this->factory->sidebar()
                    ]
                ];
                break;
            case 'upload' :
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
                        'content'    => (string)$this->factory->getFiles(),
                        'sidebar'    => (string)$this->factory->sidebar()
                    ]
                ];
                break;
        }
        return [];
    }
}