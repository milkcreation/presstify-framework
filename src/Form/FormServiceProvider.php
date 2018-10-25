<?php

namespace tiFy\Form;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Contracts\Form\FactoryField as FactoryFieldContract;
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
use tiFy\Form\Field\Tag\Tag as FieldTag;
use tiFy\Form\FieldController;
use tiFy\Form\FormFactory;
use tiFy\Form\FormManager;

class FormServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('form', function () { return new FormManager(); })->build();
        $this->app->bind(
            'form.factory',
            function ($name, $attrs = []) {
                return new FormFactory($name, $attrs);
            }
        );
        $this->app->bind(
            'form.factory.addons',
            function ($addons = [], FormFactoryContract $form) {
                return new FactoryAddons($addons, $form);
            }
        );
        $this->app->bind(
            'form.factory.buttons',
            function ($buttons = [], FormFactoryContract $form) {
                return new FactoryButtons($buttons, $form);
            }
        );
        $this->app->bind(
            'form.factory.events',
            function ($events = [], FormFactoryContract $form) {
                return new FactoryEvents($events, $form);
            }
        );
        $this->app->bind(
            'form.factory.field',
            function ($name, $attrs = [], FormFactoryContract $form) {
                return new FactoryField($name, $attrs, $form);
            }
        );
        $this->app->bind(
            'form.factory.fields',
            function ($fields = [], FormFactoryContract $form) {
                return new FactoryFields($fields, $form);
            }
        );
        $this->app->bind(
            'form.factory.notices',
            function ($notices = [], FormFactoryContract $form) {
                return new FactoryNotices($notices, $form);
            }
        );
        $this->app->bind(
            'form.factory.options',
            function ($options = [], FormFactoryContract $form) {
                return new FactoryOptions($options, $form);
            }
        );
        $this->app->bind(
            'form.factory.request',
            function (FormFactoryContract $form) {
                return new FactoryRequest($form);
            }
        );
        $this->app->bind(
            'form.factory.session',
            function (FormFactoryContract $form) {
                return new FactorySession($form);
            }
        );
        $this->app->bind(
            'form.factory.validation',
            function (FormFactoryContract $form) {
                return new FactoryValidation($form);
            }
        );

        $this->app->bind(
            'form.addon',
            function ($name, $attrs = [], FormFactoryContract $form) {
                return new AddonController($name, $attrs, $form);
            }
        );
        $this->app->bind(
            'form.addon.ajax-submit',
            function ($name, $attrs, FormFactoryContract $form) {
                return new AddonAjaxSubmit($name, $attrs, $form);
            }
        );
        $this->app->bind(
            'form.addon.cookie-session',
            function ($name, $attrs, FormFactoryContract $form) {
                return new AddonCookieSession($name, $attrs, $form);
            }
        );
        $this->app->bind(
            'form.addon.mailer',
            function ($name, $attrs, FormFactoryContract $form) {
                return new AddonMailer($name, $attrs, $form);
            }
        );
        $this->app->bind(
            'form.addon.preview',
            function ($name, $attrs, FormFactoryContract $form) {
                return new AddonPreview($name, $attrs, $form);
            }
        );
        $this->app->bind(
            'form.addon.record',
            function ($name, $attrs, FormFactoryContract $form) {
                return new AddonRecord($name, $attrs, $form);
            }
        );
        $this->app->bind(
            'form.addon.user',
            function ($name, $attrs, FormFactoryContract $form) {
                return new AddonUser($name, $attrs, $form);
            }
        );

        $this->app->bind(
            'form.button',
            function ($name, $attrs = [], FormFactoryContract $form) {
                return new ButtonController($name, $attrs, $form);
            }
        );
        $this->app->bind(
            'form.button.submit',
            function ($name, $attrs, FormFactoryContract $form) {
                return new ButtonSubmit($name, $attrs, $form);
            }
        );

        $this->app->bind(
            'form.field',
            function ($name, FactoryFieldContract $field) {
                return new FieldController($name, $field);
            }
        );
        /*$this->app->bind(
            'form.field.captcha',
            function (FactoryFieldContract $field) {
                return new FieldCaptcha($field);
            }
        );*/
        $this->app->bind(
            'form.field.html',
            function ($name, FactoryFieldContract $field) {
                return new FieldHtml($name, $field);
            }
        );
        $this->app->bind(
            'form.field.recaptcha',
            function ($name, FactoryFieldContract $field) {
                return new FieldRecaptcha($name, $field);
            }
        );
        $this->app->bind(
            'form.field.tag',
            function ($name, FactoryFieldContract $field) {
                return new FieldTag($name, $field);
            }
        );
    }
}