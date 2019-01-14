<?php

namespace tiFy\Wp\PageHook\Admin;

use tiFy\Metabox\MetaboxWpOptionsController;
use tiFy\Wp\PageHook\PageHook;

class PageHookAdminOptions extends MetaboxWpOptionsController
{
    /**
     * {@inheritdoc}
     */
    public function content($var1 = null, $var2 = null, $var3 = null)
    {
        return $this->viewer('content', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function header($var1 = null, $var2 = null, $var3 = null)
    {
        return $this->item->getTitle() ? : __('Page d\'accroche', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        /** @var  PageHook $pageHook */
        $pageHook = app()->get('wp.page-hook');
        $this->set('items', $pageHook->all());
    }

    /**
     * {@inheritdoc}
     */
    public function settings()
    {
        $settings = [];
        foreach($this->get('items', []) as $item) :
            array_push($settings, $item->getOptionName());
        endforeach;

        return $settings;
    }
}