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
            'url'      => '',
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

        switch (request()->input('action')) {
            case 'delete' :
                $file = $this->factory->getFile();

                if ($file->isDir()) {
                    $this->factory->adapter()->deleteDir($file->getRelPath());
                } else {
                    $this->factory->adapter()->delete($file->getRelPath());
                }

                $this->factory->setPath($file->getDirname());

                return [
                    'success'    => true,
                    'breadcrumb' => (string)$this->factory->breadcrumb(),
                    'content'    => (string)$this->factory->getFiles(),
                    'sidebar'    => (string)$this->factory->sidebar()
                ];
                break;
            case 'getdir' :
                return [
                    'success'    => true,
                    'breadcrumb' => (string)$this->factory->breadcrumb(),
                    'content'    => (string)$this->factory->getFiles(),
                    'sidebar'    => (string)$this->factory->sidebar(),
                ];
                break;
            case 'getfile' :
                return [
                    'success' => true,
                    'sidebar' => (string)$this->factory->sidebar(),
                ];
                break;
            case 'newdir' :
                $file = $this->factory->getFile($path);
                $path = $file->isDir() ? $path : $file->getDirname();
                $this->factory->filesystem()->createDir($path . '/' . request()->input('newdir'));

                return [
                    'success'    => true,
                    'breadcrumb' => (string)$this->factory->breadcrumb(),
                    'content'    => (string)$this->factory->getFiles(),
                ];
                break;
            case 'newname' :
                $file = $this->factory->getFile();
                $newpath = $this->factory->getFile()->getDirname() . '/' . request()->input('newname');
                $newpath .= ($file->isFile() && (request()->input('keep') === 'on') && $file->getExtension())
                    ? '.' . $file->getExtension()
                    : '';

                $this->factory->adapter()->rename($path, $newpath);

                $this->factory->setPath($newpath);

                return [
                    'success'    => true,
                    'breadcrumb' => (string)$this->factory->breadcrumb(),
                    'content'    => (string)$this->factory->getFiles(),
                    'sidebar'    => (string)$this->factory->sidebar(),
                ];
                break;
            case 'upload' :
                $file = $this->factory->getFile();
                $path = $file->isDir() ? $path : $file->getDirname();


                foreach (request()->files as $key => $f) {
                    /* @var UploadedFile $f */
                    $this->factory->filesystem()->put(
                        $path . '/' . $f->getClientOriginalName(),
                        file_get_contents($f->getPathname())
                    );
                }

                return [
                    'success'    => true,
                    'breadcrumb' => (string)$this->factory->breadcrumb(),
                    'content'    => (string)$this->factory->getFiles(),
                    'sidebar'    => (string)$this->factory->sidebar(),
                ];
                break;
        }
        return [];
    }
}