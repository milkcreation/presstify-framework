<?php declare(strict_types=1);

namespace tiFy\Wordpress\Form;

use tiFy\Contracts\Container\Container;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Contracts\Form\FormManager;
use tiFy\Contracts\Form\MailerAddonDriver as MailerAddonDriverContract;
use tiFy\Support\Arr;
use tiFy\Wordpress\Contracts\Form as FormContract;
use tiFy\Wordpress\Form\AddonDrivers\MailerAddonDriver;

class Form implements FormContract
{
    /**
     * Instance du conteneur d'injection de dépendances.
     * @var Container
     */
    protected $container;

    /**
     * Instance du controleur de gestion des formulaires.
     * @var FormManager
     */
    protected $formManager;

    /**
     * @param FormManager $formManager Instance du controleur de gestion des formulaires.
     * @param Container $container
     */
    public function __construct(FormManager $formManager, Container $container)
    {
        $this->formManager = $formManager->boot();
        $this->container = $container;

        add_action('wp', function () {
            foreach ($this->formManager->all() as $form) {
                /* @var FormFactory $form */
                $form->events()->listen('field.get.value', function(&$value) {
                    $value = Arr::stripslashes($value);
                });
            }
        });

        add_action('init', function () {
            if (is_admin()) {
                events()->trigger('wp-admin.form.boot');

                foreach ($this->formManager->all() as $form) {
                    /* @var FormFactory $form */
                    $this->formManager->current($form);
                    $form->boot();
                    $this->formManager->reset();
                }

                events()->trigger('wp-admin.form.booted');
            }
        }, 999999);

        $this->registerOverride();
    }

    /**
     * Déclaration des injections de dépendance de surchage.
     *
     * @return void
     */
    public function registerOverride(): void
    {
        $this->container->add(MailerAddonDriverContract::class, function (): MailerAddonDriverContract {
            return new MailerAddonDriver();
        });
    }
}
