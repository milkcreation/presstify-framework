<?php

namespace tiFy\PageHook\Admin;

use tiFy\TabMetabox\ContentOptionsController;
use tiFy\PageHook\PageHook;

class PageHookAdminOptions extends ContentOptionsController
{
    /**
     * Classe de rappel du controleur des pages d'accroche.
     * @var PageHook
     */
    protected $pageHook;

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->pageHook = $this->appServiceGet(PageHook::class);

        $this->set('items', $this->pageHook->all());
    }

    /**
     * {@inheritdoc}
     */
    public function settings()
    {
        $settings = [];
        if ($items = $this->pageHook->all()) :
            foreach($items as $item) :
                array_push($settings, $item->getOptionName());
            endforeach;
        endif;

        return $settings;
    }
}