<?php

declare(strict_types=1);

namespace tiFy\Mail;

use Exception;
use Pollen\Http\Response;
use tiFy\Contracts\Mail\Mailable as MailableContract;
use tiFy\Contracts\Mail\Mailer;
use tiFy\Contracts\View\Engine as ViewEngine;
use tiFy\Support\ParamsBag;

class Mailable implements MailableContract
{
    /**
     * Instance du gestionnaire de mail.
     * @var Mailer|null
     */
    private $mailer;

    /**
     * Instance des données du message.
     * @var ParamsBag
     */
    public $data;

    /**
     * Instance des paramètres.
     * @var ParamsBag
     */
    protected $params;

    /**
     * Instance du controleur de gabarit d'affichage.
     * @var ViewEngine
     */
    protected $viewEngine;

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function data($key, $value = null): MailableContract
    {
        if (!$this->data instanceof ParamsBag) {
            $this->data = new ParamsBag();
        }

        if (!is_array($key)) {
            $key = [$key => $value];
        }

        $this->data->set($key);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return $this->mailer()->getDefaults();
    }

    /**
     * @inheritDoc
     */
    public function debug(): string
    {
        $this->mailer()->create($this);

        return $this->mailer()->prepare()
            ? $this->view('debug') : $this->mailer()->getDriver()->error();
    }

    /**
     * @inheritDoc
     */
    public function mailer(): ?Mailer
    {
        if (is_null($this->mailer)) {
            try {
                $this->mailer = Mailer::instance();
            } catch (Exception $e) {
                unset($e);

                return null;
            }
        }

        return $this->mailer;
    }

    /**
     * @inheritDoc
     */
    public function params($key = null, $default = null)
    {
        if (!$this->params instanceof ParamsBag) {
            $this->params = (new ParamsBag())->set($this->defaults());
        }

        if (is_string($key)) {
            return $this->params->get($key, $default);
        } elseif (is_array($key)) {
            return $this->params->set($key);
        } else {
            return $this->params;
        }
    }

    /**
     * @inheritDoc
     */
    public function queue($date = 'now', array $params = []): int
    {
        $this->mailer()->create($this);

        return $this->mailer()->prepare()->addQueue($this, $date, $params);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return (string)$this->mailer()->prepare()->getDriver()->getHtml();
    }

    /**
     * @inheritDoc
     */
    public function response(): Response
    {
        return new Response($this->render());
    }

    /**
     * @inheritDoc
     */
    public function send(): bool
    {
        $this->mailer()->create($this);

        return $this->mailer()->prepare()->getDriver()->send();
    }

    /**
     * @inheritDoc
     */
    public function setMailer(Mailer $mailer): MailableContract
    {
        $this->mailer = $mailer;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParams(array $params): MailableContract
    {
        $this->params($params);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function view(?string $view = null, array $data = []): string
    {
        if (is_null($this->viewEngine)) {
            $this->viewEngine = $this->mailer()->resolve('mailable.view-engine');

            $this->viewEngine->params(array_merge([
                'directory' => $this->mailer()->resources('views/mailable'),
                'factory'   => MailableView::class,
                'mailable'  => $this,
            ], $this->params('viewer', [])));
        }

        if (func_num_args() === 0) {
            return $this->viewEngine;
        }

        return $this->viewEngine->render($view, array_merge($this->data ? $this->data->all() : [], $data));
    }
}
