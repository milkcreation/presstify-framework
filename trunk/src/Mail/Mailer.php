<?php declare(strict_types=1);

namespace tiFy\Mail;

use Exception;
use Html2Text\Html2Text;
use Pelago\Emogrifier\CssInliner;
use Psr\Container\ContainerInterface as Container;
use Symfony\Component\DomCrawler\Crawler;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Mail\Mailable as MailableContract;
use tiFy\Contracts\Mail\MailerDriver;
use tiFy\Contracts\Mail\Mailer as MailerContract;
use tiFy\Contracts\Mail\MailerQueue as MailerQueueContract;
use tiFy\Mail\Metabox\MailConfigMetabox;
use tiFy\Support\Arr;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Metabox;
use tiFy\Support\Proxy\Storage;
use tiFy\Validation\Validator as v;

class Mailer implements MailerContract
{
    /**
     * Instance de la classe.
     * @var static|null
     */
    private static $instance;

    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    private $booted = false;

    /**
     * Instance du gestionnaire des ressources
     * @var LocalFilesystem|null
     */
    private $resources;

    /**
     * Instance du gestionnaire de configuration.
     * @var ParamsBag
     */
    protected $config;

    /**
     * Instance du conteneur d'injection de dépendances.
     * @var Container|null
     */
    protected $container;

    /**
     * Liste des attributs de configuration par défaut.
     * @var array
     */
    protected static $defaults = [];

    /**
     * Instance du pilote d'expédition des mails.
     * @var MailerDriver
     */
    protected $driver;

    /**
     * Instance de l'email
     * @var MailableContract|null
     */
    protected $mailable;

    /**
     * Instance du gestionnaire de mise en file.
     * @var MailerQueueContract
     */
    protected $queue;

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], Container $container = null)
    {
        $this->setConfig($config);

        if (!is_null($container)) {
            $this->setContainer($container);
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * @inheritDoc
     */
    public static function instance(): MailerContract
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        throw new Exception(sprintf('Unavailable %s instance', __CLASS__));
    }

    /**
     * Traitement récursif d'une liste de contacts.
     *
     * @param string|string[]|array $contact Liste de contact.
     *
     * @return array|null
     */
    public static function parseContact($contact): ?array
    {
        $output = (func_num_args() === 2) ? func_get_arg(1) : [];

        if (is_string($contact)) {
            $email = '';
            $name = '';
            $bracket_pos = strpos($contact, '<');
            if ($bracket_pos !== false) {
                if ($bracket_pos > 0) {
                    $name = substr($contact, 0, $bracket_pos - 1);
                    $name = str_replace('"', '', $name);
                    $name = trim($name);
                }

                $email = substr($contact, $bracket_pos + 1);
                $email = str_replace('>', '', $email);
                $email = trim($email);
            } elseif (!empty($contact)) {
                $email = $contact;
            }

            if ($email && v::email()->validate($email)) {
                $output[] = array_filter([$email, $name]);
            }
        } elseif (is_array($contact)) {
            if (!Arr::isAssoc($contact)) {
                if ((count($contact) === 2) && is_string($contact[0]) && is_string($contact[1]) &&
                    v::email()->validate($contact[0]) && !v::email()->validate($contact[1])
                ) {
                    $output[] = $contact;
                } else {
                    foreach ($contact as $c) {
                        if ($value = static::parseContact($c, $output)) {
                            $output = $value;
                        }
                    }
                }
            } else {
                $email = $contact['email'] ?? null;

                if (v::email()->validate($email)) {
                    $output[] = array_filter([$email, $contact['name'] ?? null]);
                }
            }
        }

        return array_filter($output) ? : null;
    }

    /**
     * Traitement récursif d'une liste de pièces jointes.
     *
     * @param string|string[]|array $attachment
     *
     * @return array
     */
    public static function parseAttachment($attachment): array
    {
        $output = (func_num_args() === 2) ? func_get_arg(1) : [];

        if (is_string($attachment)) {
            if (is_file($attachment)) {
                $output[] = $attachment;
            }
        } elseif (is_array($attachment)) {
            foreach ($attachment as $a) {
                if (is_string($a)) {
                    $output = static::parseAttachment($a, $output);
                } elseif (is_array($a)) {
                    $filename = $a[0] ?? null;

                    if ($filename && is_file($filename)) {
                        $output[] = $a;
                    }
                }
            }
        }

        return $output;
    }

    /**
     * @inheritDoc
     */
    public static function setDefaults(array $attrs = []): void
    {
        static::$defaults = array_merge(static::$defaults, $attrs);
    }

    /**
     * @inheritDoc
     */
    public function addQueue(MailableContract $mailable, $date = 'now', array $params = []): int
    {
        return $this->getDriver()->prepare() ? $this->getQueue()->add($mailable, $date, $params) : 0;
    }

    /**
     * @inheritDoc
     */
    public function boot(): MailerContract
    {
        if (!$this->booted) {
            Metabox::registerDriver('mail-config', MailConfigMetabox::class);

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function clearDriver(): MailerContract
    {
        $this->driver = null;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function config($key = null, $default = null)
    {
        if (!isset($this->config) || is_null($this->config)) {
            $this->config = (new ParamsBag())->set($this->getDefaults());
        }

        if (is_string($key)) {
            return $this->config->get($key, $default);
        } elseif (is_array($key)) {
            return $this->config->set($key);
        } else {
            return $this->config;
        }
    }

    /**
     * @inheritDoc
     */
    public function create($attrs = null): MailableContract
    {
        if (is_null($attrs) && $this->mailable instanceof Mailable) {
            $attrs = $this->mailable;
        }

        if ($attrs instanceof Mailable) {
            return $this->mailable = $attrs->setMailer($this);
        } else {
            $this->clearDriver();

            return $this->mailable = $this->resolve('mailable')->setParams(array_merge(
                $this->config()->all(), $attrs ?: []
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function debug($attrs = null): void
    {
        echo $this->create($attrs)->debug();
        exit;
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): ?Container
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function getDefaults(string $key = null, $defaults = null)
    {
        $attrs = array_merge([
            'to'           => [],
            'from'         => [],
            'reply-to'     => [],
            'bcc'          => [],
            'cc'           => [],
            'attachments'  => [],
            'html'         => '',
            'plain'        => '',
            'data'         => [],
            'content'      => [],
            'subject'      => __('Test d\'envoi de mail', 'tify'),
            'charset'      => 'utf-8',
            'encoding'     => '8bit',
            'content_type' => 'multipart/alternative',
            'css'          => file_get_contents(__DIR__ .'/Resources/assets/css/styles.css'),
            'inline_css'   => true,
            'vars'         => [],
            'viewer'       => [],
        ], static::$defaults);

        if (!is_null($key)) {
            return $attrs[$key] ?? $defaults;
        }

        return $attrs;
    }

    /**
     * @inheritDoc
     */
    public function getDriver(): MailerDriver
    {
        if (is_null($this->driver)) {
            $this->driver = $this->resolve('driver');
        }

        return $this->driver;
    }

    /**
     * @inheritDoc
     */
    public function getQueue(): MailerQueueContract
    {
        if (is_null($this->queue)) {
            $this->queue = $this->resolve('queue');
        }

        return $this->queue;
    }

    /**
     * @inheritDoc
     */
    public function prepare(): MailerContract
    {
        $mail = $this->create();

        if ($from = $mail->params('from')) {
            $contact = static::parseContact($from)[0];

            $this->getDriver()->setFrom(...$contact);
        }

        if ($to = $mail->params('to')) {
            $contact = static::parseContact($to);

            foreach ($contact as $c) {
                $this->getDriver()->addTo(...$c);
            }
        }

        if ($replyTo = $mail->params('reply-to')) {
            $contact = static::parseContact($replyTo);

            foreach ($contact as $c) {
                $this->getDriver()->addReplyTo(...$c);
            }
        }

        if ($bcc = $mail->params('bcc')) {
            $contact = static::parseContact($bcc);

            foreach ($contact as $c) {
                $this->getDriver()->addBcc(...$c);
            }
        }

        if ($cc = $mail->params('cc')) {
            $contact = static::parseContact($cc);

            foreach ($contact as $c) {
                $this->getDriver()->addCc(...$c);
            }
        }

        if ($attachments = $mail->params('attachments', [])) {
            $files = static::parseAttachment($attachments);

            foreach ($files as $path) {
                $this->getDriver()->addAttachment($path);
            }
        }

        if ($charset = $mail->params('charset')) {
            $this->getDriver()->setCharset($charset);
        }

        if ($encoding = $mail->params('encoding')) {
            $this->getDriver()->setEncoding($encoding);
        }

        if ($content_type = $mail->params('content_type')) {
            $this->getDriver()->setContentType($content_type);
        }

        if ($subject = $mail->params('subject')) {
            $this->getDriver()->setSubject($subject);
        }

        if ($data = $mail->params('data', [])) {
            $mail->data($data);
        }

        if (!$html = $mail->params('html')) {
            if ($mail->params('content')) {
                if ($body = $mail->params('content.body', true)) {
                    $body = is_string($body) ? $body : $mail->view('html/body');
                }

                if ($header = $mail->params('content.header', true)) {
                    $header = is_string($header) ? $header : $mail->view('html/header');
                }

                if ($footer = $mail->params('content.footer', true)) {
                    $footer = is_string($footer) ? $footer : $mail->view('html/footer');
                }

                $html = $mail->view('html/content', compact('body', 'header', 'footer'));
            } else {
                $html = $mail->params('text') ?: $mail->view('html/message');
            }
        }

        if (!$text = $mail->params('text')) {
            $text = (new Html2Text($html ?: $mail->view('text/message')))->getText();
        }

        if (!(new Crawler($html))->filter('head')->count()) {
            $html = $mail->view('html/wrap-html', ['html' => $html]);
        }

        if ($css = $mail->params('inline_css')) {
            $css = is_string($css) ?: '';

            try {
                $html = CssInliner::fromHtml($html)->inlineCss($css)->render();
            } catch (Exception $e) {
                unset($e);
            }
        }

        switch ($mail->params('content_type')) {
            case 'multipart/alternative' :
                $this->getDriver()->setHtml($html);
                $this->getDriver()->setText($text);
                break;
            case 'text/html' :
                $this->getDriver()->setHtml($text);
                break;
            case 'text/plain' :
                $this->getDriver()->setText($text);
                break;
        }

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @todo
     */
    public function queue($attrs = null, $date = 'now', array $params = []): int
    {
        return $this->create($attrs)->queue($date, $params);
    }

    /**
     * @inheritDoc
     */
    public function resolve(string $alias)
    {
        return ($container = $this->getContainer()) ? $container->get("mail.{$alias}") : null;
    }

    /**
     * @inheritDoc
     */
    public function resolvable(string $alias): bool
    {
        return ($container = $this->getContainer()) && $container->has("mail.{$alias}");
    }

    /**
     * @inheritDoc
     */
    public function resources(?string $path = null)
    {
        if (!isset($this->resources) || is_null($this->resources)) {
            $this->resources = Storage::local(__DIR__ . '/Resources');
        }

        return is_null($path) ? $this->resources : $this->resources->path($path);
    }

    /**
     * @inheritDoc
     */
    public function send($attrs = null): bool
    {
        return $this->create($attrs)->send();
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $attrs): MailerContract
    {
        $this->config($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContainer(Container $container): MailerContract
    {
        $this->container = $container;

        return $this;
    }
}