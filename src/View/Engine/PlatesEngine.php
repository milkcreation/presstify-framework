<?php declare(strict_types=1);

namespace tiFy\View\Engine;

use League\Plates\Engine as BasePlatesEngine;
use LogicException;
use Throwable;
use tiFy\Contracts\View\{
    Engine as BaseEngineContract,
    PlatesEngine as PlatesEngineContract,
    PlatesFactory as PlatesFactoryContract,
    View as ViewContract
};
use tiFy\View\Factory\PlatesFactory;
use tiFy\Support\ParamsBag;

class PlatesEngine extends BasePlatesEngine implements PlatesEngineContract
{
    /**
     * Instance du gestionnaire de paramètres.
     * @var ParamsBag
     */
    protected $params;

    /**
     * Instance du gestionnaire de vue.
     * @var ViewContract|null
     */
    protected $manager;

    /**
     * CONSTRUCTEUR
     *
     * @param ViewContract $manager
     *
     * @return void
     */
    public function __construct(ViewContract $manager)
    {
        $this->manager = $manager;

        parent::__construct($this->manager()->getDefaultDirectory());
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'directory'    => null,
            'factory'      => PlatesFactory::class,
            'ext'          => 'php',
            'override_dir' => null,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFactory(string $name): PlatesFactoryContract
    {
        $factory = $this->params('factory', PlatesFactory::class);

        return new $factory($this, $name);
    }

    /**
     * @inheritDoc
     */
    public function getOverrideDir(string $path = ''): ?string
    {
        return $this->getFolders()->exists('_override')
            ? $this->getFolders()->get('_override')->getPath() . ($path ? trim($path, '/') : '')
            : null;
    }

    /**
     * @inheritDoc
     */
    public function make($name, $args = [])
    {
        $factory = $this->getFactory($name);
        $factory->data($args);

        return $factory;
    }

    /**
     * @inheritDoc
     */
    public function manager(): ?ViewContract
    {
        return $this->manager;
    }

    /**
     * @inheritDoc
     */
    public function params($key = null, $default = null)
    {
        if (is_null($this->params)) {
            $this->params = (new ParamsBag())->set($this->defaults());
        }

        if (is_string($key)) {
            return $this->params->get($key, $default);
        } elseif (is_array($key)) {
            foreach ($key as $k => $v) {
                switch ($k) {
                    case 'directory' :
                        $this->setDirectory($v);
                        break;
                    case 'ext' :
                        $this->setFileExtension($v);
                        break;
                    case 'factory' :
                        $this->setFactory($v);
                        break;
                    case 'folders' :
                        $this->setFolders($v);
                        break;
                    case 'override_dir' :
                        $this->setOverrideDir($v);
                        break;
                    default :
                        $this->params->set([$k => $v]);
                        break;
                }
            }
        }

        return $this->params;
    }

    /**
     * @inheritDoc
     */
    public function modifyFolder(string $name, string $directory, ?bool $fallback = null): PlatesEngineContract
    {
        if ($folder = $this->getFolders()->get($name)) {
            if (is_null($folder)) {
                $fallback = $folder->getFallback();
            }
            $this->removeFolder($name)->addFolder($name, $directory, $fallback);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Throwable
     */
    public function render($name, array $data = []): string
    {
        try {
            return $this->make($name)->render($data);
        } catch (LogicException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @inheritDoc
     */
    public function share($key, $value = null): PlatesEngineContract
    {
        $this->addData(is_array($key) ? $key : [$key => $value]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDirectory($directory)
    {
        $this->params()->set('directory', $directory);

        if (is_null($this->getOverrideDir())) {
            $this->setOverrideDir($directory);
        }

        return parent::setDirectory($directory);
    }

    /**
     * @inheritDoc
     */
    public function setFactory(string $factory): PlatesEngineContract
    {
        $this->params()->set('factory', $factory);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setFileExtension($fileExtension)
    {
        $this->params()->set('ext', $fileExtension);

        return parent::setFileExtension($fileExtension);
    }

    /**
     * @inheritDoc
     */
    public function setFolders(array $folders): PlatesEngineContract
    {
        foreach ($folders as $name => $dir) {
            try {
                $this->addFolder($name, $dir, false);
            } catch (LogicException $e) {
                continue;
            }
        }

        $this->params()->set('folders', $folders);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setManager(ViewContract $manager): PlatesEngineContract
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOverrideDir(string $override_dir): PlatesEngineContract
    {
        if ($override_dir) {
            $this->params()->set('override_dir', $override_dir);

            try {
                $this->addFolder('_override', $override_dir, true);
            } catch (LogicException $e) {
                if ($this->getFolders()->get('_override')->getPath() !== $override_dir) {
                    $this->modifyFolder('_override', $override_dir);
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return PlatesEngineContract
     */
    public function setParams(array $params): BaseEngineContract
    {
        $this->params($params);

        return $this;
    }
}