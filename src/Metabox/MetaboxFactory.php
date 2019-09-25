<?php declare(strict_types=1);

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\MetaboxManager;
use tiFy\Support\ParamsBag;

class MetaboxFactory extends ParamsBag
{
    /**
     * Instance du gestionnaire de metabox.
     * @var MetaboxManager|null
     */
    protected $manager;

    /**
     * @inheritDoc
     */
    public function boot(): void {}

    /**
     * @inheritDoc
     */
    public function content()
    {
        return __('Aucun contenu à afficher', 'tify');
    }

    /**
     * @inheritDoc
     */
    public function header()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function parse()
    {
        parent::parse();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParams($attrs = [])
    {
        $this->set($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setManager(MetaboxManager $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setScreen($attrs = [])
    {
        $this->set($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function viewer($view = null, $data = [])
    {
        if (!$this->viewer) {
            $this->viewer = view()
                ->setDirectory($default_dir = __DIR__ . '/Resources/views')
                ->setController(MetaboxViewer::class)
                ->setOverrideDir(
                    (($override_dir = $this->get('viewer.override_dir')) && is_dir($override_dir))
                        ? $override_dir
                        : $default_dir
                )
                ->set('metabox', $this);
        }

        if (func_num_args() === 0) {
            return $this->viewer;
        }

        return $this->viewer->make("_override::{$view}", $data);
    }
}