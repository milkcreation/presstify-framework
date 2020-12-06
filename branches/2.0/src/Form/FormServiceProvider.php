<?php declare(strict_types=1);

namespace tiFy\Form;

use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Form\AddonDriver as AddonDriverContract;
use tiFy\Contracts\Form\AddonsFactory as AddonsFactoryContract;
use tiFy\Contracts\Form\ButtonDriver as ButtonDriverContract;
use tiFy\Contracts\Form\ButtonsFactory as ButtonsFactoryContract;
use tiFy\Contracts\Form\EventsFactory as EventsFactoryContract;
use tiFy\Contracts\Form\FieldDriver as FieldDriverContract;
use tiFy\Contracts\Form\FieldsFactory as FieldsFactoryContract;
use tiFy\Contracts\Form\FieldGroupsFactory as FieldGroupsFactoryContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Contracts\Form\FormManager as FormManagerContract;
use tiFy\Contracts\Form\FormView as FormViewContract;
use tiFy\Contracts\Form\FieldGroupsFactory as GroupsFactoryContract;
use tiFy\Contracts\Form\HandleFactory as HandleFactoryContract;
use tiFy\Contracts\Form\HtmlFieldDriver as HtmlFieldDriverContract;
use tiFy\Contracts\Form\MailerAddonDriver as MailerAddonDriverContract;
use tiFy\Contracts\Form\OptionsFactory as OptionsFactoryContract;
use tiFy\Contracts\Form\RecordAddonDriver as RecordAddonDriverContract;
use tiFy\Contracts\Form\SessionFactory as SessionFactoryContract;
use tiFy\Contracts\Form\SubmitButtonDriver as SubmitButtonDriverContract;
use tiFy\Contracts\Form\TagFieldDriver as TagFieldDriverContract;
use tiFy\Contracts\Form\UserAddonDriver as UserAddonDriverContract;
use tiFy\Contracts\Form\ValidateFactory as ValidateFactoryContract;
use tiFy\Contracts\View\Engine as ViewEngine;
use tiFy\Form\AddonDrivers\MailerAddonDriver;
use tiFy\Form\AddonDrivers\RecordAddonDriver;
use tiFy\Form\AddonDrivers\UserAddonDriver;
use tiFy\Form\ButtonDrivers\SubmitButtonDriver;
use tiFy\Form\Factory\AddonsFactory;
use tiFy\Form\Factory\ButtonsFactory;
use tiFy\Form\Factory\EventsFactory;
use tiFy\Form\Factory\FieldsFactory;
use tiFy\Form\Factory\FieldGroupsFactory;
use tiFy\Form\Factory\HandleFactory;
use tiFy\Form\Factory\OptionsFactory;
use tiFy\Form\Factory\SessionFactory;
use tiFy\Form\Factory\ValidateFactory;
use tiFy\Form\FieldDrivers\HtmlFieldDriver;
use tiFy\Form\FieldDrivers\TagFieldDriver;
use tiFy\Support\Proxy\View;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        FormManagerContract::class,
        AddonDriverContract::class,
        AddonsFactoryContract::class,
        ButtonDriverContract::class,
        ButtonsFactoryContract::class,
        EventsFactoryContract::class,
        FieldDriverContract::class,
        FieldsFactoryContract::class,
        FormFactoryContract::class,
        FormManagerContract::class,
        FormViewContract::class,
        FieldGroupsFactoryContract::class,
        GroupsFactoryContract::class,
        HandleFactoryContract::class,
        HtmlFieldDriverContract::class,
        MailerAddonDriverContract::class,
        OptionsFactoryContract::class,
        RecordAddonDriverContract::class,
        SessionFactoryContract::class,
        SubmitButtonDriverContract::class,
        TagFieldDriverContract::class,
        UserAddonDriverContract::class,
        ValidateFactoryContract::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(FormManagerContract::class, function () {
            return new FormManager(config('form', []), $this->getContainer());
        });

        $this->registerAddonDrivers();
        $this->registerButtonDrivers();
        $this->registerFieldDrivers();
        $this->registerFormFactories();
        $this->registerFormView();
    }

    /**
     * @return void
     */
    public function registerAddonDrivers(): void
    {
        $this->getContainer()->add(AddonDriverContract::class, function (): AddonDriverContract {
            return new AddonDriver();
        });

        $this->getContainer()->add(MailerAddonDriverContract::class, function (): MailerAddonDriverContract{
            return new MailerAddonDriver();
        });

        $this->getContainer()->add(RecordAddonDriverContract::class, function (): RecordAddonDriverContract {
            return new RecordAddonDriver();
        });

        $this->getContainer()->add(UserAddonDriverContract::class, function (): UserAddonDriverContract {
            return new UserAddonDriver();
        });
    }

    /**
     * @return void
     */
    public function registerButtonDrivers(): void
    {
        $this->getContainer()->add(ButtonDriverContract::class, function (): ButtonDriverContract {
            return new ButtonDriver();
        });

        $this->getContainer()->add(SubmitButtonDriverContract::class, function (): SubmitButtonDriverContract {
            return new SubmitButtonDriver();
        });
    }

    /**
     * @return void
     */
    public function registerFieldDrivers(): void
    {
        $this->getContainer()->add(FieldDriverContract::class, function (): FieldDriverContract {
            return new FieldDriver();
        });

        $this->getContainer()->add(HtmlFieldDriverContract::class, function (): HtmlFieldDriverContract {
            return new HtmlFieldDriver();
        });

        $this->getContainer()->add(TagFieldDriverContract::class, function (): TagFieldDriverContract {
            return new TagFieldDriver();
        });
    }

    /**
     * @return void
     */
    public function registerFormFactories(): void
    {
        $this->getContainer()->add(FormFactoryContract::class, function (): FormFactoryContract {
            return new BaseFormFactory();
        });

        $this->getContainer()->add(AddonsFactoryContract::class, function (): AddonsFactoryContract {
            return new AddonsFactory();
        });

        $this->getContainer()->add(ButtonsFactoryContract::class, function (): ButtonsFactoryContract {
            return new ButtonsFactory();
        });

        $this->getContainer()->add(EventsFactoryContract::class, function (): EventsFactoryContract {
            return new EventsFactory();
        });

        $this->getContainer()->add(FieldsFactoryContract::class, function (): FieldsFactoryContract {
            return new FieldsFactory();
        });

        $this->getContainer()->add(FieldGroupsFactoryContract::class, function (): FieldGroupsFactoryContract {
            return new FieldGroupsFactory();
        });

        $this->getContainer()->add(OptionsFactoryContract::class, function (): OptionsFactoryContract {
            return new OptionsFactory();
        });

        $this->getContainer()->add(HandleFactoryContract::class, function (): HandleFactoryContract {
            return new HandleFactory();
        });

        $this->getContainer()->add(SessionFactoryContract::class, function (): SessionFactoryContract {
            return new SessionFactory();
        });

        $this->getContainer()->add(ValidateFactoryContract::class, function (): ValidateFactoryContract {
            return new ValidateFactory();
        });
    }

    /**
     * @return void
     */
    public function registerFormView(): void
    {
        $this->getContainer()->add(FormViewContract::class, function (): ViewEngine {
            return View::getPlatesEngine();
        });
    }
}