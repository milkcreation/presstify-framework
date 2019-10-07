<?php declare(strict_types=1);

namespace tiFy\Wordpress\PageHook;

use tiFy\Contracts\Metabox\MetaboxDriver as MetaboxDriverContract;
use tiFy\Metabox\{MetaboxView, MetaboxDriver};
use tiFy\Wordpress\Contracts\{PageHook, PageHookItem};

class PageHookMetabox extends MetaboxDriver
{
    /**
     * Instance du gestionnaire de page d'accroche.
     * @var PageHook|null
     */
    protected $pageHook;

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'title' => __('Pages d\'accroche', 'tify')
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parse(): MetaboxDriverContract
    {
        parent::parse();

        $this->set([
            'items' => $this->pageHook()->collect()->where('admin', true)->all()
        ]);

        return $this;
    }

    /**
     * Récupération du gestionnaire de pages d'accroche.
     *
     * @return PageHook
     */
    public function pageHook(): ?PageHook
    {
        return $this->pageHook;
    }

    /**
     * Définition du gestionnaire de pages d'accroche.
     *
     * @return $this
     */
    public function setPageHook(PageHook $pageHook): self
    {
       $this->pageHook = $pageHook;

       return $this;
    }

    /**
     * @inheritDoc
     */
    public function settings()
    {
        $settings = [];
        foreach($this->get('items', []) as $item) {
            /** @var PageHookItem $item */
            array_push($settings, $item->getOptionName());
        }

        return $settings;
    }

    /**
     * @inheritDoc
     */
    public function viewer(?string $view = null, array $data = [])
    {
        if (!$this->viewer) {
            $defaultDir = __DIR__ . '/Resources/views/metabox/';
            $fallbackDir = $this->get('viewer.override_dir') ?: $defaultDir;

            $this->viewer = view()
                ->setDirectory($defaultDir)
                ->setOverrideDir($fallbackDir)
                ->setController(MetaboxView::class)
                ->set('metabox', $this);
        }

        if (func_num_args() === 0) {
            return $this->viewer;
        }

        return $this->viewer->make("_override::{$view}", $data);
    }
}