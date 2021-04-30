<?php declare(strict_types=1);

namespace tiFy\Form;

use LogicException;
use tiFy\Contracts\Form\AddonsFactory as AddonsFactoryContract;
use tiFy\Contracts\Form\ButtonsFactory as ButtonsFactoryContract;
use tiFy\Contracts\Form\EventsFactory as EventsFactoryContract;
use tiFy\Contracts\Form\FieldsFactory as FieldsFactoryContract;
use tiFy\Contracts\Form\FieldGroupsFactory as FieldGroupsFactoryContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Contracts\Form\FormManager as FormManagerContract;
use tiFy\Contracts\Form\FormView as FormViewContract;
use tiFy\Contracts\Form\HandleFactory as HandleFactoryContract;
use tiFy\Contracts\Form\OptionsFactory as OptionsFactoryContract;
use tiFy\Contracts\Form\SessionFactory as SessionFactoryContract;
use tiFy\Contracts\Form\ValidateFactory as ValidateFactoryContract;
use tiFy\Contracts\Http\Request as RequestContract;
use tiFy\Contracts\View\Engine as ViewEngine;
use tiFy\Form\Concerns\FactoryBagTrait;
use tiFy\Form\Factory\AddonsFactory;
use tiFy\Form\Factory\ButtonsFactory;
use tiFy\Form\Factory\EventsFactory;
use tiFy\Form\Factory\FieldsFactory;
use tiFy\Form\Factory\FieldGroupsFactory;
use tiFy\Form\Factory\HandleFactory;
use tiFy\Form\Factory\OptionsFactory;
use tiFy\Form\Factory\SessionFactory;
use tiFy\Form\Factory\ValidateFactory;
use tiFy\Support\Concerns\LabelsBagTrait;
use tiFy\Support\Concerns\MessagesBagTrait;
use tiFy\Support\Concerns\ParamsBagTrait;
use tiFy\Support\LabelsBag;
use tiFy\Support\MessagesBag;
use tiFy\Support\Proxy\Asset;
use tiFy\Support\Proxy\View;
use tiFy\Support\Proxy\Request;

class BaseFormFactory implements FormFactoryContract
{
    use FactoryBagTrait, LabelsBagTrait, MessagesBagTrait, ParamsBagTrait;

    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    private $built = false;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Instance du gestionnaire de formulaire.
     * @var FormManagerContract
     */
    private $formManager;

    /**
     * Indicateur d'initialisation de rendu.
     * @var array
     */
    private $renderBuild = [
        'attrs'   => false,
        'fields'  => false,
        'id'      => false,
        'notices' => false,
    ];

    /**
     * Instance du gestionnaire de validation.
     * @var FormViewContract|null
     */
    private $view;

    /**
     * Alias de qualification.
     * @var string
     */
    protected $alias = '';

    /**
     * Instance de gestion des intitulés.
     * @var LabelsBag|null
     */
    protected $labelsBag;

    /**
     * Instance de la requête de traitement .
     * @var RequestContract|null
     */
    protected $request;

    /**
     * Indicateur de succès de soumission du formulaire.
     * @var boolean
     */
    protected $successed = false;

    /**
     * Nom de qualification du formulaire dans les attributs de balises HTML.
     * @var string|null
     */
    protected $tagName;

    /**
     * Instance du moteur de gabarits d'affichage.
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
    public function boot(): FormFactoryContract
    {
        if (!$this->isBooted()) {
            $this->event('form.boot', [&$this]);

            $this->parseParams();

            $services = [
                'events',
                'addons',
                'fields',
                'groups',
                'buttons',
                'options',
                'session',
                'validate'
            ];

            foreach ($services as $service) {
                $this->{$service}->boot();
            }

            if ($successed = filter_var($this->session()->pull('successed', false), FILTER_VALIDATE_BOOL)) {
                $this->setSuccessed($successed);
            }

            $this->booted = true;

            $this->event('form.booted', [&$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function build(): FormFactoryContract
    {
        if (!$this->isBuilt()) {
            if (!$this->formManager instanceof FormManagerContract) {
                throw new LogicException('Unavailable FormFactory related FormManager');
            } else {
                $fM = $this->formManager;
            }

            if ($this->addons === null) {
                $this->setAddonsFactory($fM->resolvable(AddonsFactoryContract::class)
                    ? $fM->resolve(AddonsFactoryContract::class) : new AddonsFactory()
                );
            }
            $this->addons()->setForm($this);

            if ($this->buttons === null) {
                $this->setButtonsFactory($fM->resolvable(ButtonsFactoryContract::class)
                    ? $fM->resolve(ButtonsFactoryContract::class) : new ButtonsFactory()
                );
            }
            $this->buttons()->setForm($this);

            if ($this->events === null) {
                $this->setEventsFactory($fM->resolvable(EventsFactoryContract::class)
                    ? $fM->resolve(EventsFactoryContract::class) : new EventsFactory()
                );
            }
            $this->events()->setForm($this);

            if ($this->fields === null) {
                $this->setFieldsFactory($fM->resolvable(FieldsFactoryContract::class)
                    ? $fM->resolve(FieldsFactoryContract::class) : new FieldsFactory()
                );
            }
            $this->fields()->setForm($this);

            if ($this->groups === null) {
                $this->setGroupsFactory($fM->resolvable(FieldGroupsFactoryContract::class)
                    ? $fM->resolve(FieldGroupsFactoryContract::class) : new FieldGroupsFactory()
                );
            }
            $this->groups()->setForm($this);

            if ($this->handle === null) {
                $this->setHandleFactory($fM->resolvable(HandleFactoryContract::class)
                    ? $fM->resolve(HandleFactoryContract::class) : new HandleFactory()
                );
            }
            $this->handle()->setForm($this);

            if ($this->options === null) {
                $this->setOptionsFactory($fM->resolvable(OptionsFactoryContract::class)
                    ? $fM->resolve(OptionsFactoryContract::class) : new OptionsFactory()
                );
            }
            $this->options()->setForm($this);

            if ($this->session === null) {
                $this->setSessionFactory($fM->resolvable(SessionFactoryContract::class)
                    ? $fM->resolve(SessionFactoryContract::class) : new SessionFactory()
                );
            }
            $this->session()->setForm($this);

            if ($this->validate === null) {
                $this->setValidateFactory($fM->resolvable(ValidateFactoryContract::class)
                    ? $fM->resolve(ValidateFactoryContract::class) : new ValidateFactory()
                );
            }
            $this->validate()->setForm($this);

            $this->built = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function csrf(): string
    {
        return wp_create_nonce('Form' . $this->getAlias());
    }

    /**
     * @inheritDoc
     */
    public function defaultLabels(): array
    {
        return [
            'gender'   => $this->params('labels.gender', false),
            'plural'   => $this->params('labels.plural', $this->getTitle()),
            'singular' => $this->params('labels.singular', $this->getTitle()),
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            /**
             * @var string $action Propriété 'action' de la balise <form/>.
             */
            'action'   => '',
            /**
             * @var array $addons Liste des attributs des addons actifs.
             */
            'addons'   => [],
            /**
             * @var string $after Post-affichage, après la balise <form/>.
             */
            'after'    => '',
            /**
             * @var array $attrs Liste des attributs complémentaires de la balise <form/>.
             */
            'attrs'    => [],
            /**
             * @var string $before Pré-affichage, avant la balise <form/>.
             */
            'before'   => '',
            /**
             * @var array $buttons Liste des attributs des boutons actifs.
             */
            'buttons'  => [],
            /**
             * @var string $enctype Propriété 'enctype' de la balise <form/>.
             */
            'enctype'  => '',
            /**
             * @var array $events Liste des événements de court-circuitage.
             */
            'events'   => [],
            /**
             * @var array $fields Liste des attributs de champs.
             */
            'fields'   => [],
            /**
             * @var string $method Propriété 'method' de la balise <form/>.
             */
            'method'   => 'post',
            /**
             * @var array $options Liste des options du formulaire.
             */
            'options'  => [],
            /**
             * @var string[] $supports Propriété de support.
             */
            'supports' => ['session'],
            /**
             * @var string $title Intitulé de qualification du formulaire.
             */
            'title'    => $this->getAlias(),
            /**
             * @var array $viewer Attributs de configuration du gestionnaire de gabarits d'affichage.
             */
            'viewer'   => [],
            /**
             * @var array $wrapper Attributs de configuration de l'encapsulation du formulaire.
             */
            'wrapper'  => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function error(string $message, array $datas = []): string
    {
        return $this->messages($message, 'error', $datas);
    }

    /**
     * @inheritDoc
     */
    public function formManager(): ?FormManagerContract
    {
        return $this->formManager;
    }

    /**
     * @inheritDoc
     */
    public function getAction(): string
    {
        return (string)$this->params('action');
    }

    /**
     * @inheritDoc
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @inheritDoc
     */
    public function getAnchor(): string
    {
        if ($anchor = $this->option('anchor')) {
            if (!is_string($anchor)) {
                if ($this->renderBuildWrapper() && ($exists = $this->params('wrapper.attrs.id'))) {
                    $anchor = $exists;
                } elseif ($this->renderBuildId() && ($exists = $this->params('attrs.id'))) {
                    $anchor = $exists;
                } else {
                    $anchor = '';
                }
            }

            if ($anchor) {
                return ltrim($anchor, '#');
            }
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function getIndex(): int
    {
        return $this->formManager()->getIndex($this->getAlias());
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        $method = strtolower($this->params('method'));

        return in_array($method, ['get', 'post']) ? $method : 'post';
    }

    /**
     * @inheritDoc
     */
    public function getSupports(): array
    {
        return (array)$this->params('supports', []);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return (string)$this->params('title');
    }

    /**
     * @inheritDoc
     */
    public function hasError(): bool
    {
        return $this->messages()->has('error');
    }

    /**
     * @inheritDoc
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * @inheritDoc
     */
    public function isBuilt(): bool
    {
        return $this->built;
    }

    /**
     * @inheritDoc
     */
    public function isSubmitted(): bool
    {
        return $this->handle()->isSubmitted();
    }

    /**
     * @inheritDoc
     */
    public function isSuccessed(): bool
    {
        return $this->successed;
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): FormFactoryContract
    {
        return $this->parseLabels();
    }

    /**
     * @inheritDoc
     */
    public function onSetCurrent(): void
    {
        $this->event('form.set.current', [&$this]);
    }

    /**
     * @inheritDoc
     */
    public function onResetCurrent(): void
    {
        $this->event('form.reset.current', [&$this]);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->renderBuild();

        $groups = $this->groups();
        $fields = $this->fields()->preRender();
        $buttons = $this->buttons();
        $notices = $this->messages()->fetchRenderMessages([
            $this->messages()::ERROR   => 'error',
            $this->messages()::INFO    => 'info',
            $this->messages()::NOTICE  => 'success',
            $this->messages()::WARNING => 'warning'
        ]);

        return $this->view('index', compact('buttons', 'fields', 'groups', 'notices'));
    }

    /**
     * @inheritDoc
     */
    public function renderBuild(): FormFactoryContract
    {
        return $this
            ->renderBuildId()
            ->renderBuildWrapper()
            ->renderBuildAttrs()
            ->renderBuildNotices();
    }

    /**
     * @inheritDoc
     */
    public function renderBuildAttrs(): FormFactoryContract
    {
        if ($this->renderBuild['attrs'] === false) {
            $param = $this->params();

            $default_class = "FormContent FormContent--{$this->tagName()}";
            if (!$param->has('attrs.class')) {
                $param->set('attrs.class', $default_class);
            } else {
                $param->set('attrs.class', sprintf($param->get('attrs.class'), $default_class));
            }
            if (!$param->get('attrs.class')) {
                $param->pull('attrs.class');
            }

            $param->set('attrs.action', $this->getAction());

            $param->set('attrs.method', $this->getMethod());
            if ($enctype = $param->get('enctype')) {
                $param->set('attrs.enctype', $enctype);
            }

            $this->renderBuild['attrs'] = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function renderBuildId(): FormFactoryContract
    {
        if ($this->renderBuild['id'] === false) {
            $param = $this->params();

            if (!$param->has('attrs.id')) {
                $param->set('attrs.id', "FormContent--{$this->tagName()}");
            }
            if (!$param->get('attrs.id')) {
                $param->pull('attrs.id');
            }

            $this->renderBuild['id'] = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function renderBuildNotices(): FormFactoryContract
    {
        if ($this->renderBuild['notices'] === false) {
            if ($this->messages()->count()) {
                $this->session()->forget('notices');
            } elseif ($notices = $this->session()->pull('notices')) {
                foreach ($notices as $type => $items) {
                    foreach ($items as $item) {
                        $this->messages()->add(
                            MessagesBag::convertLevel($type), $item['message'] ?? '', $item['datas'] ?? []
                        );
                    }
                }
            }

            if ($this->isSuccessed()) {
                if (!$this->messages()->has('success')) {
                    $this->messages()->success($this->option('success.message', ''));
                }

                $this->session()->destroy();
            } else {
                $this->session()->forget('notices');
            }

            Asset::setInlineJs(
                'window.addEventListener("load", (event) => {' .
                'if(window.location.href.split("#")[1] === "' . $this->getAnchor() . '"){' .
                'window.history.pushState("", document.title, window.location.pathname + window.location.search);' .
                '}});', true);

            $this->renderBuild['successed'] = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function renderBuildWrapper(): FormFactoryContract
    {
        if (($this->renderBuild['wrapper'] ?? false) !== true) {
            $param = $this->params();

            $wrapper = $param->get('wrapper');

            if ($wrapper !== false) {
                $param->set('wrapper', array_merge([
                    'tag' => 'div',
                ], is_array($wrapper) ? $wrapper : []));

                if (!$param->has('wrapper.attrs.id')) {
                    $param->set('wrapper.attrs.id', 'Form--' . $this->tagName());
                }

                if (!$param->has('wrapper.attrs.class')) {
                    $param->set('wrapper.attrs.class', 'Form');
                }
            }

            $this->renderBuild['wrapper'] = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function request(): RequestContract
    {
        if ($this->request === null) {
            $this->request = Request::instance();
        }

        return $this->request;
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): FormFactoryContract
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setFormManager(FormManagerContract $formManager): FormFactoryContract
    {
        $this->formManager = $formManager;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHandleRequest(RequestContract $request): FormFactoryContract
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSuccessed(bool $status = true): FormFactoryContract
    {
        $this->successed = $status;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $support)
    {
        return in_array($support, $this->getSupports(), true);
    }

    /**
     * @inheritDoc
     */
    public function tagName(): string
    {
        return $this->tagName = is_null($this->tagName)
            ? lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_', '.'], ' ', $this->getAlias()))))
            : $this->tagName;
    }

    /**
     * @inheritDoc
     */
    public function view(?string $view = null, array $data = [])
    {
        if (is_null($this->viewEngine)) {
            $this->viewEngine = $this->formManager()->resolvable(FormViewContract::class)
                ? $this->formManager()->resolve(FormViewContract::class) : View::getPlatesEngine();

            $defaultConfig = $this->formManager()->config('default.viewer', []);

            if (isset($defaultConfig['directory'])) {
                $defaultConfig['directory'] = rtrim($defaultConfig['directory'], '/') . '/';

                if (!file_exists($defaultConfig['directory'])) {
                    unset($defaultConfig['directory']);
                }
            }

            if (isset($defaultConfig['override_dir'])) {
                $defaultConfig['override_dir'] = rtrim($defaultConfig['override_dir'], '/') . '/';

                if (!file_exists($defaultConfig['override_dir'])) {
                    unset($defaultConfig['override_dir']);
                }
            }

            $this->viewEngine->params(array_merge([
                'directory' => $this->formManager()->resources('views'),
                'factory'   => FormView::class,
                'form'      => $this,
            ], $defaultConfig, $this->params('viewer', [])));
        }

        if (func_num_args() === 0) {
            return $this->viewEngine;
        }

        return $this->viewEngine->render($view, $data);
    }
}