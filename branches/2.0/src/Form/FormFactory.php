<?php declare(strict_types=1);

namespace tiFy\Form;

use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Contracts\Form\FormManager;
use tiFy\Form\Factory\ResolverTrait;
use tiFy\Support\LabelsBag;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Asset;

class FormFactory extends ParamsBag implements FormFactoryContract
{
    use ResolverTrait;

    /**
     * Liste des instances de formulaire démarré.
     * @var FormFactory[]
     */
    private static $instance = [];

    /**
     * Instance de gestion des intitulés.
     * @var LabelsBag|null
     */
    protected $labels;

    /**
     * Instance du gestionnaire de formulaire.
     * @var string
     */
    protected $manager = '';

    /**
     * Nom de qualification du formulaire.
     * @var string
     */
    protected $name = '';

    /**
     * Indicateur de préparation.
     * @var boolean
     */
    protected $prepared = false;

    /**
     * Indicateur de statut du formulaire en succès.
     * @var boolean
     */
    protected $successed = false;

    /**
     * Indicateur d'initialisation de rendu.
     * @var array
     */
    protected $renderBuild = [];

    /**
     * Nom de qualification du formulaire dans les attributs de balises HTML.
     * @var string|null
     */
    protected $tagName;

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return (string)$this->render();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

    /**
     * @inheritDoc
     */
    public function csrf(): string
    {
        return wp_create_nonce('Form' . $this->name());
    }

    /**
     * Listes des attributs de configuration par défaut.
     * @return array {
     * @var string $action Propriété 'action' de la balise <form/>.
     * @var array $addons Liste des attributs des addons actifs.
     * @var array $attrs Liste des attributs complémentaires de la balise <form/>.
     * @var string $after Post-affichage, après la balise <form/>.
     * @var string $before Pré-affichage, avant la balise <form/>.
     * @var array $buttons Liste des attributs des boutons actifs.
     * @var string $enctype Propriété 'enctype' de la balise <form/>.
     * @var array $events Liste des événements de court-circuitage.
     * @var array $fields Liste des attributs de champs.
     * @var boolean|array $grid Activation de l'agencement des éléments sur grille.
     * @var string $method Propriété 'method' de la balise <form/>.
     * @var array $notices Liste des attributs des messages de notification.
     * @var array $options Liste des options du formulaire.
     * @var string $title Intitulé de qualification du formulaire.
     * @var array $viewer Attributs de configuration du gestionnaire de gabarits d'affichage.
     * @var array $wrapper Attributs de configuration de l'encapsulation du formulaire.
     * }
     */
    public function defaults(): array
    {
        return [
            'action'   => '',
            'addons'   => [],
            'after'    => '',
            'attrs'    => [],
            'before'   => '',
            'buttons'  => [],
            'enctype'  => '',
            'events'   => [],
            'fields'   => [],
            'grid'     => false,
            'method'   => 'post',
            'notices'  => [],
            'options'  => [],
            'supports' => ['session'],
            'title'    => '',
            'viewer'   => [],
            'wrapper'  => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function error(string $message, array $datas = []): string
    {
        return $this->form()->notices()->add('error', $message, $datas);
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function fieldTagsValue($tags, $raw = true)
    {
        if (is_string($tags)) {
            if (preg_match_all('/([^%%]*)%%(.*?)%%([^%%]*)?/', $tags, $matches)) :
                $tags = '';
                foreach ($matches[2] as $i => $slug) {
                    $tags .= $matches[1][$i] . (($field = $this->field($slug)) ? $field->getValue($raw) : $matches[2][$i]) . $matches[3][$i];
                }
            endif;
        } elseif (is_array($tags)) {
            foreach ($tags as $k => &$i) {
                $i = $this->fieldTagsValue($i, $raw);
            }
        }

        return $tags;
    }

    /**
     * @inheritDoc
     */
    public function getAction(): string
    {
        return $this->get('action', '');
    }

    /**
     * @inheritDoc
     */
    public function getAnchor(): string
    {
        if ($anchor = $this->option('anchor')) {
            if (!is_string($anchor)) {
                if ($this->renderBuildWrapper() && ($exists = $this->get('wrapper.attrs.id'))) {
                    $anchor = $exists;
                } elseif ($this->renderBuildId() && ($exists = $this->get('attrs.id'))) {
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
    public function getMethod(): string
    {
        $method = strtolower($this->get('method'));

        return in_array($method, ['get', 'post']) ? $method : 'post';
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->get('title') ?: $this->name();
    }

    /**
     * @inheritDoc
     */
    public function hasError(): bool
    {
        return $this->notices()->has('error');
    }

    /**
     * @inheritDoc
     */
    public function hasGrid()
    {
        return !empty($this->get('grid'));
    }

    /**
     * @inheritDoc
     */
    public function index()
    {
        return form()->index($this->name());
    }

    /**
     * @inheritDoc
     */
    public function isPrepared(): bool
    {
        return $this->prepared;
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
    public function label($key = null, string $default = '')
    {
        if (!$this->labels instanceof LabelsBag) {
            $this->labels = LabelsBag::createFromAttrs([
                'gender'   => $this->get('labels.gender', false),
                'plural'   => $this->get('labels.plural', $this->getTitle()),
                'singular' => $this->get('labels.singular', $this->getTitle()),
            ]);
        }

        if (is_string($key)) {
            return $this->labels->get($key, $default);
        } elseif (is_array($key)) {
            return $this->labels->set($key);
        } else {
            return $this->labels;
        }
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return strval($this->name);
    }

    /**
     * @inheritDoc
     */
    public function onSetCurrent(): void
    {
        $this->events('form.set.current', [&$this]);
    }

    /**
     * @inheritDoc
     */
    public function onResetCurrent(): void
    {
        $this->events('form.reset.current', [&$this]);
    }

    /**
     * @inheritDoc
     */
    public function prepare(): FormFactoryContract
    {
        if (!$this->isPrepared()) {
            $this->events('form.prepare', [&$this]);

            $this->boot();

            $this->parse();

            $services = [
                'events',
                'addons',
                'buttons',
                'fields',
                'groups',
                'handle',
                'notices',
                'options',
                'session',
                'validation',
                'viewer',
            ];

            foreach ($services as $service) {
                $this->resolve("factory.{$service}." . $this->name());
            }

            $this->setSuccessed(!!$this->session()->pull('successed', false));

            $this->groups()->prepare();

            foreach ($this->fields() as $field) {
                $field->prepare();
            }

            $this->prepared = true;

            $this->events('form.prepared', [&$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if (!$this->prepared) {
            $this->prepare();
        }

        $this->renderBuild();

        $groups = $this->groups()->getGrouped();
        $fields = $this->fields();
        $buttons = $this->buttons();
        $notices = $this->notices()->getMessages();

        return (string)$this->viewer('index', compact('buttons', 'fields', 'groups', 'notices'));
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
            ->renderBuildFields()
            ->renderBuildNotices();
    }

    /**
     * @inheritDoc
     */
    public function renderBuildAttrs(): FormFactoryContract
    {
        if (($this->renderBuild['attrs'] ?? false) !== true) {
            $default_class = "FormContent FormContent--{$this->tagName()}";
            if (!$this->has('attrs.class')) {
                $this->set('attrs.class', $default_class);
            } else {
                $this->set('attrs.class', sprintf($this->get('attrs.class', ''), $default_class));
            }
            if (!$this->get('attrs.class')) {
                $this->pull('attrs.class');
            }

            $this->set('attrs.action', $this->getAction());

            $this->set('attrs.method', $this->getMethod());
            if ($enctype = $this->get('enctype')) {
                $this->set('attrs.enctype', $enctype);
            }

            if ($grid = $this->get('grid')) {
                $grid = is_array($grid) ? $grid : [];

                $this->set("attrs.data-grid", 'true');
                $this->set("attrs.data-grid_gutter", $grid['gutter'] ?? 0);
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
        if (($this->renderBuild['id'] ?? false) !== true) {
            if (!$this->has('attrs.id')) {
                $this->set('attrs.id', "FormContent--{$this->tagName()}");
            }
            if (!$this->get('attrs.id')) {
                $this->pull('attrs.id');
            }

            $this->renderBuild['id'] = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function renderBuildFields(): FormFactoryContract
    {
        if (($this->renderBuild['fields'] ?? false) !== true) {
            foreach ($this->fields() as $field) {
                $field->renderPrepare();
            }

            $this->renderBuild['fields'] = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function renderBuildNotices(): FormFactoryContract
    {
        if ((($this->renderBuild['notices'] ?? false) !== true)) {
            if ($notices = $this->session()->pull('notices')) {
                foreach ($notices as $type => $items) {
                    foreach ($items as $item) {
                        $this->notices()->add($type, $item['message'] ?? '', $item['datas'] ?? []);
                    }
                }
            }

            if ($this->isSuccessed()) {
                if (!$this->notices()->has('success')) {
                    $this->notices()->add('success', $this->notices()->params('success.message'));
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
            $wrapper = $this->get('wrapper');

            if ($wrapper !== false) {
                $this->set('wrapper', array_merge([
                    'tag' => 'div',
                ], is_array($wrapper) ? $wrapper : []));

                if (!$this->has('wrapper.attrs.id')) {
                    $this->set('wrapper.attrs.id', 'Form--' . $this->tagName());
                }

                if (!$this->has('wrapper.attrs.class')) {
                    $this->set('wrapper.attrs.class', 'Form');
                }
            }

            $this->renderBuild['wrapper'] = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setInstance(string $name, FormManager $manager): FormFactoryContract
    {
        if (!isset(self::$instance[$name])) {
            self::$instance[$name] = $this;

            $this->name = $name;
            $this->manager = $manager;
            $this->form = $this;

            app()->share("form.factory.events.{$this->name}", function () {
                return $this->resolve('factory.events', [$this->get('events', []), $this]);
            });

            app()->share("form.factory.addons.{$this->name}", function () {
                return $this->resolve('factory.addons', [$this->get('addons', []), $this]);
            });

            app()->share("form.factory.buttons.{$this->name}", function () {
                return $this->resolve('factory.buttons', [$this->get('buttons', []), $this]);
            });

            app()->share("form.factory.fields.{$this->name}", function () {
                return $this->resolve('factory.fields', [$this->get('fields', []), $this]);
            });

            app()->share("form.factory.groups.{$this->name}", function () {
                return $this->resolve('factory.groups', [$this->get('groups', []), $this]);
            });

            app()->share("form.factory.notices.{$this->name}", function () {
                return $this->resolve('factory.notices', [$this->get('notices', []), $this]);
            });

            app()->share("form.factory.options.{$this->name}", function () {
                return $this->resolve('factory.options', [$this->get('options', []), $this]);
            });

            app()->share("form.factory.handle.{$this->name}", function () {
                return $this->resolve('factory.handle', [$this]);
            });

            app()->share("form.factory.session.{$this->name}", function () {
                return $this->resolve('factory.session', [$this]);
            });

            app()->share("form.factory.validation.{$this->name}", function () {
                return $this->resolve('factory.validation', [$this]);
            });

            app()->share("form.factory.viewer.{$this->name}", function () {
                return $this->resolve('factory.viewer', [$this]);
            });

            $this->events('form.init', [&$this]);
        }
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
    public function supports(?string $service = null)
    {
        return is_null($service) ? $this->get('supports', []) : in_array($service, $this->get('supports', []));
    }

    /**
     * @inheritDoc
     */
    public function tagName(): string
    {
        return $this->tagName = is_null($this->tagName)
            ? lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_', '.'], ' ', $this->name()))))
            : $this->tagName;
    }
}