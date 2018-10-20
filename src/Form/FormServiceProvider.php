<?php

namespace tiFy\Form;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Contracts\Form\FormFactory as FormFactoryInterface;
use tiFy\Contracts\Form\FactoryField as FactoryFieldInterface;
use tiFy\Form\Addon\AjaxSubmit\AjaxSubmit as AddonAjaxSubmit;
use tiFy\Form\Addon\CookieTransport\CookieTransport as AddonCookieTransport;
use tiFy\Form\Addon\Mailer\Mailer as AddonMailer;
use tiFy\Form\Addon\Manager as AddonManager;
use tiFy\Form\Addon\Preview\Preview as AddonPreview;
use tiFy\Form\Addon\Record\Record as AddonRecord;
use tiFy\Form\Addon\User\User as AddonUser;
use tiFy\Form\Button\Manager as ButtonManager;
use tiFy\Form\Button\Submit\Submit as ButtonSubmit;
use tiFy\Form\Factory\Addons as FactoryAddons;
use tiFy\Form\Factory\Buttons as FactoryButtons;
use tiFy\Form\Factory\Display as FactoryDisplay;
use tiFy\Form\Factory\Events as FactoryEvents;
use tiFy\Form\Factory\Field as FactoryField;
use tiFy\Form\Factory\Fields as FactoryFields;
use tiFy\Form\Factory\Notices as FactoryNotices;
use tiFy\Form\Factory\Options as FactoryOptions;
use tiFy\Form\Factory\Session as FactorySession;
use tiFy\Form\Factory\Validation as FactoryValidation;
use tiFy\Form\Factory\Viewer as FactoryViewer;
use tiFy\Form\Field\Manager as FieldManager;
use tiFy\Form\Field\Captcha\Captcha as FieldCaptcha;
use tiFy\Form\Field\Defaults as FieldDefaults;
use tiFy\Form\Field\Html\Html as FieldHtml;
use tiFy\Form\Field\Recaptcha\Recaptcha as FieldRecaptcha;
use tiFy\Form\FormFactory;
use tiFy\Form\Manager;

class FormServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('form', function () { return new Manager(); })->build();
        $this->app->bind(
            'form.factory',
            function ($name, $attrs = []) {
                return new FormFactory($name, $attrs);
            }
        );
        $this->app->bind(
            'form.factory.addons',
            function ($addons = [], FormFactoryInterface $form) {
                return new FactoryAddons($addons, $form);
            }
        );
        $this->app->bind(
            'form.factory.buttons',
            function ($buttons = [], FormFactoryInterface $form) {
                return new FactoryButtons($buttons, $form);
            }
        );
        $this->app->bind(
            'form.factory.display',
            function (FormFactoryInterface $form) {
                return new FactoryDisplay($form);
            }
        );
        $this->app->bind(
            'form.factory.events',
            function ($events = [], FormFactoryInterface $form) {
                return new FactoryEvents($events, $form);
            }
        );
        $this->app->bind(
            'form.factory.field',
            function ($name, $attrs = [], FormFactoryInterface $form) {
                return new FactoryField($name, $attrs, $form);
            }
        );
        $this->app->bind(
            'form.factory.fields',
            function ($fields = [], FormFactoryInterface $form) {
                return new FactoryFields($fields, $form);
            }
        );
        $this->app->bind(
            'form.factory.notices',
            function ($notices = [], FormFactoryInterface $form) {
                return new FactoryNotices($notices, $form);
            }
        );
        $this->app->bind(
            'form.factory.options',
            function ($options = [], FormFactoryInterface $form) {
                return new FactoryOptions($options, $form);
            }
        );
        $this->app->bind(
            'form.factory.session',
            function (FormFactoryInterface $form) {
                return new FactorySession($form);
            }
        );
        $this->app->bind(
            'form.factory.validation',
            function (FormFactoryInterface $form) {
                return new FactoryValidation($form);
            }
        );

        $this->app->singleton('form.addon', function () { return new AddonManager(); })->build();
        $this->app->bind(
            'form.addon.mailer',
            function () {
                return new AddonMailer();
            }
        );
        $this->app->bind(
            'form.addon.record',
            function () {
                return new AddonRecord();
            }
        );
        $this->app->bind(
            'form.addon.user',
            function () {
                return new AddonUser();
            }
        );

        $this->app->singleton('form.button', function () { return new ButtonManager(); })->build();
        $this->app->bind(
            'form.button.submit',
            function () {
                return new ButtonSubmit();
            }
        );

        $this->app->singleton('form.field', function () { return new FieldManager(); })->build();
        /*$this->app->bind(
            'form.field.captcha',
            function (FactoryFieldInterface $field) {
                return new FieldCaptcha($field);
            }
        );*/
        $this->app->bind(
            'form.field.defaults',
            function ($name, FactoryFieldInterface $field) {
                return new FieldDefaults($name, $field);
            }
        );
        $this->app->bind(
            'form.field.html',
            function (FactoryFieldInterface $field) {
                return new FieldHtml($field);
            }
        );
        $this->app->bind(
            'form.field.recaptcha',
            function (FactoryFieldInterface $field) {
                return new FieldRecaptcha($field);
            }
        );
    }
}