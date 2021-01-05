<?php declare(strict_types=1);

namespace tiFy\Wordpress\PageHook;

use tiFy\Metabox\Contracts\MetaboxContract;
use tiFy\Metabox\MetaboxDriver;
use tiFy\Wordpress\Contracts\PageHook;
use tiFy\Wordpress\Proxy\PageHook as ProxyPageHook;

class PageHookMetabox extends MetaboxDriver
{
    /**
     * Instance du gestionnaire de page d'accroche.
     * @var PageHook|null
     */
    protected $pageHook;

    /**
     * @param PageHook $pageHookManager
     * @param MetaboxContract $metaboxManager
     */
    public function __construct(PageHook $pageHookManager, MetaboxContract $metaboxManager)
    {
        $this->setPageHook($pageHookManager);

        parent::__construct($metaboxManager);
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            'items' => [],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? __('Pages d\'accroche', 'tify');
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        parent::parse();

        if (!$this->get('items')) {
            $this->set([
                'items' => $this->pageHook() ? $this->pageHook()->collect()->where('admin', true)->all() : [],
            ]);
        }
        return parent::render();
    }

    /**
     * Récupération du gestionnaire de pages d'accroche.
     *
     * @return PageHook
     */
    public function pageHook(): ?PageHook
    {
        return $this->pageHook ?: ProxyPageHook::getInstance();
    }

    /**
     * Définition du gestionnaire de pages d'accroche.
     *
     * @param PageHook $pageHook
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
    public function viewDirectory(): string
    {
        return class_info($this->pageHook())->getDirname() . '/Resources/views/metabox';
    }
}