<?php

declare(strict_types=1);

namespace tiFy\Form\Factory;

use InvalidArgumentException;
use LogicException;
use tiFy\Http\RedirectResponse;
use tiFy\Contracts\Form\HandleFactory as HandleFactoryContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Form\Concerns\FormAwareTrait;
use tiFy\Form\Exception\FieldValidateException;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ParamsBagTrait;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Request;
use tiFy\Support\Proxy\Url;

class HandleFactory implements HandleFactoryContract
{
    use BootableTrait;
    use FormAwareTrait;
    use ParamsBagTrait;

    /**
     * Indicateur de soumission du formulaire.
     * @var bool|null
     */
    private $submitted;

    /**
     * Instance des données de requête HTTP de traitement du formulaire.
     * @var ParamsBag
     */
    protected $datasBag;

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
        if (!$this->isBooted()) {
            if (!$this->form() instanceof FormFactoryContract) {
                throw new LogicException('Missing valid FormFactory');
            }

            $this->form()->event('handle.boot', [&$this]);

            $this->form()->session()->forget(['notices', 'request']);
            $this->form()->messages()->flush();

            switch ($accessor = $this->form()->getMethod()) {
                case 'get':
                    $accessor = 'query';
                    break;
                case 'post':
                    $accessor = 'request';
                    break;
            }

            $this->datas($this->form()->request()->{$accessor}->all());

            foreach ($this->form()->fields() as $field) {
                $value = $this->datas($field->getName());

                if (!is_null($value)) {
                    $field->setValue($value);

                    if ($field->supports('session') && $this->form()->supports('session')) {
                        $this->form()->session()->put("request.{$field->getName()}", $value);
                    }
                }
            }

            $this->setBooted();

            $this->form()->event('handle.booted', [&$this]);
        }

        return $this;
    }

    /**
     * Définition|Récupération|Instance des données de requête HTTP de traitement du formulaire.
     *
     * @param array|string|null $key
     * @param mixed $default
     *
     * @return string|int|array|mixed|ParamsBag
     *
     * @throws InvalidArgumentException
     */
    public function datas($key = null, $default = null)
    {
        if (!$this->datasBag instanceof ParamsBag) {
            $this->datasBag = new ParamsBag();
        }

        if (is_null($key)) {
            return $this->datasBag;
        }

        if (is_string($key)) {
            return $this->datasBag->get($key, $default);
        }

        if (is_array($key)) {
            $this->datasBag->set($key);
            return $this->datasBag;
        }

        throw new InvalidArgumentException('Invalid Form Handle DatasBag passed method arguments');
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

        foreach ($this->form()->messages()->all() as $type => $notices) {
            $this->form()->session()->put("notices.$type", $notices);
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
            $this->submitted = (bool)wp_verify_nonce($this->getToken(), 'Form' . $this->form()->getAlias())
                && $this->form()->request()->isMethod($this->form()->getMethod());
        }

        if ($this->submitted) {
            $this->boot();
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
        }
        $this->boot();

        $this->validate();

        if (!$this->isValidated()) {
            $this->fail();

            return null;
        }
        $this->success();

        return $this->redirect();
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

        $message = $this->form()->option('success.message', '');

        $this->form()->session()->put('notices.success', [
            [
                'message' => $message
            ]
        ]);

        $this->form()->messages()->success($message);

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
                    $without[] = $field->getName();
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
        foreach ($this->form()->fields() as $field) {
            try {
                $field->validate();
            } catch (FieldValidateException $e) {
                $field->error($e->getMessage());
            }
        }

        return $this;
    }
}
