<?php declare(strict_types=1);

namespace tiFy\Form\Factory;

use LogicException;
use tiFy\Http\RedirectResponse;
use tiFy\Contracts\Form\HandleFactory as HandleFactoryContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Form\Concerns\FormAwareTrait;
use tiFy\Form\FieldValidateException;
use tiFy\Support\Concerns\ParamsBagTrait;
use tiFy\Support\Proxy\Request;
use tiFy\Support\Proxy\Url;

class HandleFactory implements HandleFactoryContract
{
    use FormAwareTrait, ParamsBagTrait;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Indicateur de soumission du formulaire.
     * @var bool|null
     */
    private $submitted;

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
     * @inheritDoc
     */
    public function boot(): HandleFactoryContract
    {
        if ($this->booted === false) {
            if (!$this->form() instanceof FormFactoryContract) {
                throw new LogicException('Missing valid FormFactory');
            }

            $this->form()->event('handle.boot', [&$this]);

            $this->form()->session()->forget(['notices', 'request']);
            $this->form()->messages()->flush();

            switch ($method = $this->form()->getMethod()) {
                case 'get' :
                    $method = 'query';
                    break;
                case 'post' :
                    $method = 'post';
                    break;
            }

            $values = $this->form()->request()->{$method}();

            foreach ($this->form()->fields() as $field) {
                $value = $values[$field->getName()] ?? null;

                if (!is_null($value)) {
                    $field->setValue($value);

                    if ($this->form()->supports('session') && $field->supports('session')) {
                        $this->form()->session()->put("request.{$field->getName()}", $value);
                    }
                }
            }

            $this->booted = true;

            $this->form()->event('handle.booted', [&$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fail(): HandleFactoryContract
    {
        foreach ($this->form()->fields() as $field) {
            if (!$field->supports('transport')) {
                $field->resetValue();
            }
        }

        $this->form()->session()->forget('notices');

        foreach($this->form()->messages()->all() as $type => $notices) {
            $this->form()->session()->put("notices.{$type}", $notices);
        }

        $this->form()->event('handle.failed', [&$this]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRedirectUrl(): string
    {
        if (is_null($this->redirect)) {
            $this->setRedirectUrl($this->params('_http_referer', Request::header('referer')));
        }

        $this->form()->event('handle.redirect', [&$this->redirect]);

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
    public function isSubmitted(): bool
    {
        if ($this->submitted === null) {
            $this->submitted = !!wp_verify_nonce($this->getToken(), 'Form' . $this->form()->getAlias())
            && $this->form()->request()->isMethod($this->form()->getMethod());
        }

        return $this->submitted;
    }

    /**
     * @inheritDoc
     */
    public function isValidated(): bool
    {
        if (!$this->form()->messages()->has('error')) {
            $this->form()->event('handle.validated', [&$this]);

            return !$this->form()->messages()->has('error');
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function response(): ?RedirectResponse
    {
        if (!$this->isValidated()) {
            return null;
        } else {
            $this->boot();

            $this->validate();

            if (!$this->isValidated()) {
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
    public function redirect(): RedirectResponse
    {
        return new RedirectResponse($this->getRedirectUrl());
    }

    /**
     * @inheritDoc
     */
    public function success(): HandleFactoryContract
    {
        $this->form()->session()->flush();
        $this->form()->setSuccessed()->session()->put('successed', true);

        $this->form()->messages()->success($this->form()->option('success.message', ''));

        $this->form()->event('handle.successed', [&$this]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setRedirectUrl(string $url, bool $raw = false): HandleFactoryContract
    {
        if (!$raw) {
            $uri = Url::set($url);

            if ($this->form()->getMethod() === 'get') {
                $without = ['_token'];
                foreach ($this->form()->fields() as $field) {
                    array_push($without, $field->getName());
                }
                $uri = $uri->without($without);
            }

            $url = $uri->withFragment($this->form()->getAnchor())->render();
        }

        $this->redirect = $url;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate(): HandleFactoryContract
    {
        foreach ($this->form()->fields() as $name => $field) {
            try {
                $field->validate();
            } catch(FieldValidateException $e) {
                $field->error($e->getMessage());
            }
        }

        return $this;
    }
}