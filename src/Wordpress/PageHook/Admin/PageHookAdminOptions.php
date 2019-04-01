<?php

namespace tiFy\Wordpress\PageHook\Admin;

use tiFy\Wordpress\Contracts\PageHookItem;
use tiFy\Metabox\MetaboxWpOptionsController;

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

        $this->set('items', page_hook()->all());
    }

    /**
     * {@inheritdoc}
     */
    public function settings()
    {
        $settings = [];
        foreach($this->get('items', []) as $item) :
            /** @var PageHookItem $item */
            array_push($settings, $item->getOptionName());
        endforeach;

        return $settings;
    }
}