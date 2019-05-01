<?php

namespace tiFy\Wordpress\Form\Addon\Mailer;

use tiFy\Form\Addon\Mailer\Mailer as tiFyMailer;

class Mailer extends tiFyMailer
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        parent::boot();

        if ($this->get('enqueue_scripts') && ($this->get('admin.confirmation') || $this->get('admin.notification'))) {
            add_action('admin_enqueue_scripts', function () {
                field('repeater')->enqueue_scripts();
                field('toggle-switch')->enqueue_scripts();
            });
        }
    }

    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return array_merge(parent::defaults(), ['enqueue_scripts' => false]);
    }
}