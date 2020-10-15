<?php declare(strict_types=1);

namespace tiFy\Form\Factory;

use tiFy\Http\RedirectResponse;
use tiFy\Contracts\Form\{FactoryHandle, FormFactory};
use tiFy\Support\{ParamsBag, Proxy\Request, Proxy\Url};

class Handle extends ParamsBag implements FactoryHandle
{
    use ResolverTrait;

    /**
     * Indicateur de traitement effectué.
     * @var boolean
     */
    protected $processing = false;

    /**
     * Url de redirection.
     * @var string
     */
    protected $redirect;

    /**
     * Clé d'indice de la protection CSRF.
     * @var string
     */
    protected $tokenKey = '_token';

    /**
     * CONSTRUCTEUR.
     *
     * @param FormFactory $form
     *
     * @return void
     */
    public function __construct(FormFactory $form)
    {
        $this->form = $form;
    }

    /**
     * @inheritDoc
     */
    public function fail(): FactoryHandle
    {
        foreach ($this->fields() as $field) {
            if (!$field->supports('transport')) {
                $field->resetValue();
            }
        }

        $this->events('handle.failed', [&$this]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRedirectUrl(): string
    {
        if (is_null($this->redirect)) {
            $this->setRedirectUrl($this->get('_http_referer', Request::header('referer')));
        }

        $this->events('handle.redirect', [&$this->redirect]);

        return $this->redirect;
    }

    /**
     * @inheritDoc
     */
    public function getToken(): string
    {
        return Request::input($this->tokenKey, '');
    }

    /**
     * @inheritDoc
     */
    public function response(): ?RedirectResponse
    {
        if (!$this->verify()) {
            return null;
        } else {
            $this->prepare();

            $this->validate();

            if ($this->notices()->has('error')) {
                $this->fail();

                return null;
            } else {
                $this->success();

                return $this->redirect();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function prepare(): FactoryHandle
    {
        $this->form()->prepare();

        switch ($method = $this->form()->getMethod()) {
            case 'get' :
                $method = 'query';
                break;
            case 'post' :
                $method = 'post';
                break;

        }

        $values = call_user_func([Request::getInstance(), $method]);

        foreach ($this->fields() as $field) {
            $value = $values[$field->getName()] ?? null;

            if (!is_null($value)) {
                $this->set($field->getSlug(), $value);

                $field->setValue($value);

                if ($field->supports('session')) {
                    $this->form()->session()->put($field->getName(), $value);
                }
            }
        }

        $this->parse();

        $this->events('handle.prepared', [&$this]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function redirect(): RedirectResponse
    {
        return new RedirectResponse($this->getRedirectUrl());
    }

    /**
     * @inheritDoc
     */
    public function success(): FactoryHandle
    {
        $this->form()->session()->destroy();

        $this->events('handle.successed', [&$this]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setRedirectUrl(string $url, bool $raw = false): FactoryHandle
    {
        if (!$raw) {
            $uri = Url::set($url);

            if ($this->form()->getMethod() === 'get') {
                $without = ['_token'];
                foreach ($this->fields() as $field) {
                    array_push($without, $field->getName());
                }
                $uri = $uri->without($without);
            }

            $uri = $uri->with(['success' => $this->form()->name()]);

            $url = $uri->render() . $this->form()->getAnchor();
        }

        $this->redirect = $url;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate(): FactoryHandle
    {
        foreach ($this->fields() as $name => $field) {
            $check = true;

            if ($field->getRequired('check')) {
                $value = $field->getValue($field->getRequired('raw', true));

                if (!$check = $this->validation()->call(
                    $field->getRequired('call'), $value,
                    $field->getRequired('args', []))
                ) {
                    $this->notices()->add('error', sprintf($field->getRequired('message'), $field->getTitle()), [
                        'type'  => 'field',
                        'field' => $field->getSlug(),
                        'test'  => 'required',
                    ]);
                }
            }

            if ($check) {
                if ($validations = $field->get('validations', [])) {
                    $value = $field->getValue($field->getRequired('raw', true));

                    foreach ($validations as $validation) {
                        if (!$this->validation()->call($validation['call'], $value, $validation['args'])) {
                            $this->notices()->add('error', sprintf($validation['message'], $field->getTitle()), [
                                'field' => $field->getSlug(),
                            ]);
                        }
                    }
                }
            }

            $this->events('handle.validate.field.' . $field->getType(), [&$field]);
            $this->events('handle.validate.field', [&$field]);
        }

        if (!$this->notices()->has('error')) {
            $this->events('handle.validated', [&$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function verify(): bool
    {
        if ($this->processing) {
            return false;
        } elseif($verified = !!wp_verify_nonce($this->getToken(), 'Form' . $this->form()->name())) {
            $this->processing = true;
        }

        $this->events('handle.verified', [&$this]);

        return $verified;
    }
}