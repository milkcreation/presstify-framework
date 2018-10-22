<?php

namespace tiFy\Form;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Contracts\Form\FormFactory as FormFactoryInterface;
use tiFy\Contracts\Form\FactoryField as FactoryFieldInterface;
use tiFy\Form\Addon\AjaxSubmit\AjaxSubmit as AddonAjaxSubmit;
use tiFy\Form\Addon\CookieSession\CookieSession as AddonCookieSession;
use tiFy\Form\Addon\Mailer\Mailer as AddonMailer;
use tiFy\Form\Addon\Preview\Preview as AddonPreview;
use tiFy\Form\Addon\Record\Record as AddonRecord;
use tiFy\Form\Addon\User\User as AddonUser;
use tiFy\Form\AddonController;
use tiFy\Form\Button\Submit\Submit as ButtonSubmit;
use tiFy\Form\ButtonController;
use tiFy\Form\Factory\Addons as FactoryAddons;
use tiFy\Form\Factory\Buttons as FactoryButtons;
use tiFy\Form\Factory\Display as FactoryDisplay;
use tiFy\Form\Factory\Events as FactoryEvents;
use tiFy\Form\Factory\Field as FactoryField;
use tiFy\Form\Factory\Fields as FactoryFields;
use tiFy\Form\Factory\Notices as FactoryNotices;
use tiFy\Form\Factory\Options as FactoryOptions;
use tiFy\Form\Factory\Request as FactoryRequest;
use tiFy\Form\Factory\Session as FactorySession;
use tiFy\Form\Factory\Validation as FactoryValidation;
use tiFy\Form\Field\Captcha\Captcha as FieldCaptcha;
use tiFy\Form\Field\Html\Html as FieldHtml;
use tiFy\Form\Field\Recaptcha\Recaptcha as FieldRecaptcha;
use tiFy\Form\FieldController;
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
            'form.factory.request',
            function (FormFactoryInterface $form) {
                return new FactoryRequest($form);
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

        $this->app->bind(
            'form.addon',
            function ($name, $attrs = [], FormFactoryInterface $form) {
                return new AddonController($name, $attrs, $form);
            }
        );
        $this->app->bind(
            'form.addon.ajax-submit',
            function ($attrs, FormFactoryInterface $form) {
                return new AddonAjaxSubmit($attrs, $form);
            }
        );
        $this->app->bind(
            'form.addon.cookie-session',
            function ($attrs, FormFactoryInterface $form) {
                return new AddonCookieSession($attrs, $form);
            }
        );
        $this->app->bind(
            'form.addon.mailer',
            function ($attrs, FormFactoryInterface $form) {
                return new AddonMailer($attrs, $form);
            }
        );
        $this->app->bind(
            'form.addon.preview',
            function ($attrs, FormFactoryInterface $form) {
                return new AddonPreview($attrs, $form);
            }
        );
        $this->app->bind(
            'form.addon.record',
            function ($attrs, FormFactoryInterface $form) {
                return new AddonRecord($attrs, $form);
            }
        );
        $this->app->bind(
            'form.addon.user',
            function ($attrs, FormFactoryInterface $form) {
                return new AddonUser($attrs, $form);
            }
        );

        $this->app->bind(
            'form.button',
            function ($name, $attrs = [], FormFactoryInterface $form) {
                return new ButtonController($name, $attrs, $form);
            }
        );
        $this->app->bind(
            'form.button.submit',
            function ($attrs, FormFactoryInterface $form) {
                return new ButtonSubmit($attrs, $form);
            }
        );

        $this->app->bind(
            'form.field',
            function ($name, FactoryFieldInterface $field) {
                return new FieldController($name, $field);
            }
        );
        /*$this->app->bind(
            'form.field.captcha',
            function (FactoryFieldInterface $field) {
                return new FieldCaptcha($field);
            }
        );*/
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