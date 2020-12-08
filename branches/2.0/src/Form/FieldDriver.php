<?php declare(strict_types=1);

namespace tiFy\Form;

use Closure, LogicException;
use tiFy\Contracts\Form\FieldDriver as FieldDriverContract;
use tiFy\Contracts\Form\FieldGroupDriver as FieldGroupDriverContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Form\Concerns\FormAwareTrait;
use tiFy\Support\Arr;
use tiFy\Support\Concerns\ParamsBagTrait;
use tiFy\Support\Proxy\Field;

class FieldDriver implements FieldDriverContract
{
    use FormAwareTrait, ParamsBagTrait;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    private $built = false;

    /**
     * Liste des propriétés de support par défaut.
     * @var array
     */
    private $defaultSupports = ['label', 'request', 'session', 'tabindex', 'transport', 'wrapper'];

    /**
     * Liste des attributs de support des types de champs natifs.
     * @var array
     */
    private $fieldTypeSupports = [
        'button'              => ['request', 'wrapper'],
        'checkbox'            => ['checking', 'label', 'request', 'wrapper', 'session', 'tabindex', 'transport'],
        'checkbox-collection' => ['choices', 'label', 'request', 'session', 'tabindexes', 'transport', 'wrapper'],
        'datetime-js'         => ['label', 'request', 'session', 'tabindexes', 'transport', 'wrapper'],
        'file'                => ['label', 'request', 'tabindex', 'wrapper'],
        'hidden'              => ['request', 'session', 'transport'],
        'label'               => ['wrapper'],
        'password'            => ['label', 'request', 'tabindex', 'wrapper'],
        'radio'               => ['label', 'request', 'session', 'tabindex', 'transport', 'wrapper'],
        'radio-collection'    => ['choices', 'label', 'request', 'session', 'tabindexes', 'transport', 'wrapper'],
        'repeater'            => ['label', 'request', 'session', 'tabindexes', 'transport', 'wrapper'],
        'select'              => ['choices', 'label', 'request', 'session', 'tabindex', 'transport', 'wrapper'],
        'select-js'           => ['choices', 'label', 'request', 'session', 'tabindex', 'transport', 'wrapper'],
        'submit'              => ['request', 'tabindex', 'wrapper'],
        'toggle-switch'       => ['request', 'tabindex', 'session', 'transport', 'wrapper'],
    ];

    /**
     * Indicateur de pré-traitement du rendu.
     * @bool
     */
    protected $renderable = false;
    
    /**
     * Alias de qualification.
     * @var string
     */
    protected $alias;

    /**
     * Valeur par défaut.
     * @var mixed
     */
    protected $default;

    /**
     * Indicateur du statut d'affichage de champ en erreur.
     * @var boolean
     */
    protected $error = false;

    /**
     * Identifiant de qualification du champ.
     * @var string
     */
    protected $slug = '';

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
    public function boot(): FieldDriverContract
    {
        if (!$this->isBooted()) {
           if (!$this->form() instanceof FormFactoryContract) {
                throw new LogicException('Invalid related FormFactory');
            }

            $this->form()->event('field.boot.' . $this->getType(), [&$this]);
            $this->form()->event('field.boot', [&$this]);

            $this->parseParams();

            $this->form()->event('field.booted.' . $this->getType(), [&$this]);
            $this->form()->event('field.booted', [&$this]);

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function build(): FieldDriverContract
    {
        if (!$this->isBuilt()) {
            if ($this->alias === null) {
                throw new LogicException('Missing alias');
            }

            $this->built = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addNotice(string $message, string $type = 'error', array $datas = []): FieldDriverContract
    {
        $this->form()->messages($message, $type, array_merge($datas, ['field' => $this->getSlug()]));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function after(): string
    {
        $after = $this->params('after');

        return $after instanceof Closure ? $after($this) : strval($after);
    }

    /**
     * @inheritDoc
     */
    public function before(): string
    {
        $before = $this->params('before');

        return $before instanceof Closure ? $before($this) : strval($before);
    }

    /**
     * @inheritDoc
     */
    public function error(string $message, array $datas = []): FieldDriverContract
    {
        return $this->addNotice($message, 'error', $datas);
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            /**
             * @var array $addons Liste des attributs de configuration associés aux addons.
             */
            'addons'      => [],
            /**
             * @var string $after Contenu HTML affiché après le champ.
             */
            'after'       => '',
            /**
             * @var array $attrs Liste des attributs de la balise HTML, hors name et value.
             */
            'attrs'       => [],
            /**
             * @var string $before Contenu HTML affiché avant le champ.
             */
            'before'      => '',
            /**
             * @var array $choices Liste de choix des valeurs multiples.
             */
            'choices'     => [],
            /**
             * @var array $extras Liste des attributs de configuration complémentaires.
             */
            'extras'      => [],
            /**
             * @var string $group Alias du groupe d'appartenance.
             */
            'group'       => '',
            /**
             * @var bool|string|array $label Affichage de l'intitulé de champ. false si masqué|true charge les attributs
             * par défaut|array permet de définir des attributs personnalisés.
             */
            'label'       => true,
            /**
             * @var string $name Indice de qualification de la variable de requête.
             */
            'name'        => $this->getSlug(),
            /**
             * @var int $position Ordre d'affichage général ou dans le groupe s'il est défini.
             */
            'position'    => 0,
            /**
             * @var boolean|string|array $required {
             * Configuration de champs requis. false si désactivé|true charge les attributs par défaut|array
             * @type boolean|string|array $tagged Affichage de l'indicateur de champ requis. false si masqué|true charge
             * les attributs par défaut|string valeur de l'indicateur|array permet de définir des attributs personnalisés.
             * @type boolean $check Activation du test d'existance natif.
             * @type mixed $value_none Valeur à comparer pour le test d'existance.
             * @type string|callable $call Fonction de validation ou alias de qualification.
             * @type array $args Liste des variables passées en argument dans la fonction de validation.
             * @type boolean $raw Activation du format brut de la valeur.
             * @type string $message Message de notification de retour en cas d'erreur.
             * }
             */
            'required'    => false,
            /**
             * @var null|boolean $session Court-circuitage de la propriété de support du stockage en session des données à
             * l'issue de la soumission.
             */
            'session'     => true,
            /**
             * @var array $supports Définition des propriétés de support. label|wrapper|request|tabindex|transport.
             */
            'supports'    => [],
            /**
             * @var string $title Intitulé de qualification. Valeur par défaut. ex. label.
             */
            'title'       => $this->getSlug(),
            /**
             * @var boolean|null $transport Court-circuitage de la propriété de support du transport des données à
             * l'issue de la soumission.
             */
            'transport'   => null,
            /**
             * @var string $type Type de champ.
             */
            'type'        => 'html',
            /**
             * @var array $validations {
             * Liste des fonctions de validation d'intégrité du champ lors de la soumission.
             * @type string|callable $call Fonction de validation ou alias de qualification.
             * @type array $args Liste des variables passées en arguments dans la fonction de validation.
             * @type string $message Message de notification d'erreur.
             * @type boolean $raw Activation du format brut de la valeur.
             * }
             */
            'validations' => [],
            /**
             * @var mixed $value Valeur courante de la variable de requête.
             */
            'value'       => '',
            /**
             * @var bool|string|array $wrapper Affichage de l'encapuleur de champ. false si masqué|true charge les attributs
             * par défaut|array permet de définir des attributs personnalisés.
             */
            'wrapper'     => null,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAddonOption(string $alias, ?string $key = null, $default = null)
    {
        return is_null($key) ? $this->params("addons.{$alias}", []) : $this->params("addons.{$alias}.{$key}", $default);
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
    public function getDefault()
    {
       return ($default = $this->default) instanceof Closure ? $default($this): $default;
    }

    /**
     * @inheritDoc
     */
    public function getExtras(?string $key = null, $default = null)
    {
        return is_null($key) ? $this->params('extras', []) : $this->params("extras.{$key}", $default);
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): ?FieldGroupDriverContract
    {
        return $this->form()->group((string)$this->params('group'));
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->params('name');
    }

    /**
     * @inheritDoc
     */
    public function getNotices(?string $type = null): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getPosition(): int
    {
        return (int)$this->params('position', 0);
    }

    /**
     * @inheritDoc
     */
    public function getRequired(?string $key = null, $default = null)
    {
        return $this->params('required' . ($key ? ".{$key}" : ''), $default);
    }

    /**
     * @inheritDoc
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Récupération de la liste des attibuts de support.
     *
     * @return array
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
    public function getType(): string
    {
        return (string)$this->params('type');
    }

    /**
     * @inheritDoc
     */
    public function getValue(bool $raw = true)
    {
        $value = $this->params('value');

        $this->form()->event('field.get.value', [&$value, $this]);

        if (!$raw) {
            $value = is_array($value) ? array_map('esc_attr', $value) : esc_attr($value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getValues(bool $raw = true, ?string $glue = ', ')
    {
        $value = Arr::wrap($this->getValue());

        if ($choices = $this->params('choices', [])) {
            foreach ($value as &$v) {
                if (isset($choices[$v])) {
                    $v = $choices[$v];
                }
            }
        }

        if (!$raw) {
            $value = is_array($value) ? array_map('esc_attr', $value) : esc_attr($value);
        }

        if (!is_null($glue)) {
            $value = join($glue, $value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function hasLabel(): bool
    {
        return $this->supports('label') && !empty($this->params('label'));
    }

    /**
     * @inheritDoc
     */
    public function hasNotices(?string $type = null): bool
    {
        $messageBag = $this->form()->messages();

        return $messageBag->hasMessages($type ? $messageBag::convertLevel($type) : null, ['field' => $this->getSlug()]);
    }

    /**
     * @inheritDoc
     */
    public function hasWrapper(): bool
    {
        return $this->supports('wrapper') && !empty($this->params('wrapper'));
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
    public function isRenderable(): bool
    {
        return $this->renderable;
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): FieldDriver
    {
        $param = $this->params();

        if ($this->default === null) {
            $this->setDefault($param->get('value', null));
        }

        $name = $param->get('name', null);
        if (!is_null($name)) {
            $param->set(['name' => $name ? esc_attr($name) : esc_attr($this->getSlug())]);
        }

        if (!$param->get('supports')) {
            $param->set('supports', $this->fieldTypeSupports[$this->getType()] ?? $this->defaultSupports);
        }

        $transport = $param->get('transport');
        if ($transport && !in_array('transport', $param->get('supports', []))) {
            $param->push('supports', 'transport');
        } elseif ($transport === false) {
            $param->set('supports', array_diff($param->get('supports', []), ['transport']));
        }

        $session = $param->get('session');
        if ($session && !in_array('session', $param->get('supports', []))) {
            $param->push('supports', 'session');
        } elseif ($session === false) {
            $param->set('supports', array_diff($param->get('supports', []), ['session']));
        }

        $this->setSessionValue();

        if ($param->get('wrapper')) {
            $param->push('supports', 'wrapper');
        } elseif (in_array('wrapper', $param->get('supports', []))) {
            $param->set('wrapper', true);
        }

        if ($required = $param->get('required', false)) {
            $required = (is_array($required)) ? $required : (is_string($required) ? ['message' => $required] : []);
            $required = array_merge([
                'tagged'     => true,
                'check'      => true,
                'value_none' => '',
                'call'       => '',
                'args'       => [],
                'raw'        => true,
                'message'    => __('Le champ "%s" doit être renseigné.', 'tify'),
                'html5'      => false,
            ], $required);

            if ($tagged = $required['tagged']) {
                $tagged = is_array($tagged) ? $tagged : (is_string($tagged) ? ['content' => $tagged] : []);
                $required['tagged'] = array_merge([
                    'tag'     => 'span',
                    'attrs'   => [],
                    'content' => '*',
                ], $tagged);
            }

            $required['call'] = !empty($required['value_none']) && empty($required['call']) ? '!equals' : 'notEmpty';
            $required['args'] = !empty($required['value_none']) && empty($required['args'])
                ? [] + [$required['value_none']]
                : [];

            $param->set('required', $required);
        }

        if ($validations = $param->get('validations')) {
            $param->set('validations', $this->parseValidations($validations));
        }

        foreach ($this->form()->addons() as $alias => $addon) {
            $param->set("addons.{$alias}", array_merge(
                $addon->defaultFieldOptions(), $param->get("addons.{$alias}", []) ?: []
            ));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseValidations($validations, array $results = []): array
    {
        if (is_array($validations)) {
            if (isset($validations['call'])) {
                $results[] = array_merge([
                    'alias'   => '',
                    'args'    => [],
                    'call'    => '__return_true',
                    'message' => __('Le format du champ "%s" est invalide', 'tify'),
                    'raw'     => false,
                ], $validations);
            } else {
                foreach ($validations as $validation) {
                    $results += $this->parseValidations($validation, $results);
                }
            }
        } elseif (is_string($validations)) {
            $validations = array_map('trim', explode(',', $validations));

            foreach ($validations as $call) {
                $results += $this->parseValidations(['call' => $call], $results);
            }
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function preRender(): FieldDriverContract
    {
        if (!$this->isRenderable()) {

            $param = $this->params();

            if (!$param->has('attrs.id')) {
                $param->set('attrs.id', "FormField-input--{$this->getSlug()}_{$this->form()->getIndex()}");
            }

            if (!$param->get('attrs.id')) {
                $param->pull('attrs.id');
            }

            $default_class = "%s FormField-input FormField-input--{$this->getType()} FormField-input--{$this->getSlug()}";

            if (!$param->has('attrs.class')) {
                $param->set('attrs.class', $default_class);
            } else {
                $param->set('attrs.class', sprintf($param->get('attrs.class'), $default_class));
            }

            if (!$param->get('attrs.class')) {
                $param->pull('attrs.class');
            }

            if (!$param->has('attrs.tabindex')) {
                $param->set('attrs.tabindex', $this->getPosition());
            }

            if ($param->get('attrs.tabindex') === false) {
                $param->pull('attrs.tabindex');
            }

            if ($this->hasNotices('error')) {
                $param->set('attrs.aria-invalid', 'true');
            }

            if ($wrapper = $param->get('wrapper')) {
                $wrapper = (is_array($wrapper)) ? $wrapper : [];
                $param->set('wrapper', array_merge(['tag' => 'div', 'attrs' => []], $wrapper));

                if (!$param->has('wrapper.attrs.id')) {
                    $param->set('wrapper.attrs.id', "FormField--{$this->getSlug()}_{$this->form()->getIndex()}");
                }
                if (!$param->get('wrapper.attrs.id')) {
                    $param->pull('wrapper.attrs.id');
                }

                $default_class = "FormField FormField--{$this->getType()} FormField--{$this->getSlug()}";
                if (!$param->has('wrapper.attrs.class')) {
                    $param->set('wrapper.attrs.class', $default_class);
                } else {
                    $param->set('wrapper.attrs.class', sprintf($param->get('wrapper.attrs.class'), $default_class));
                }
                if (!$param->get('wrapper.attrs.class')) {
                    $param->pull('wrapper.attrs.class');
                }
            }

            if ($param->get('required.tagged')) {
                if (!$param->has('required.tagged.attrs.id')) {
                    $param->set('required.tagged.attrs.id',
                        "FormField-required--{$this->getSlug()}_{$this->form()->getIndex()}");
                }
                if (!$param->get('required.tagged.attrs.id')) {
                    $param->pull('required.tagged.attrs.id');
                }

                $default_class = "%s FormField-required FormField-required--{$this->getType()} FormField-required--{$this->getSlug()}";
                if (!$param->has('required.tagged.attrs.class')) {
                    $param->set('required.tagged.attrs.class', $default_class);
                } else {
                    $param->set(
                        'required.tagged.attrs.class',
                        sprintf($param->get('required.tagged.attrs.class'), $default_class)
                    );
                }
                if (!$param->get('required.tagged.attrs.class')) {
                    $param->pull('required.tagged.attrs.class');
                }
            }

            if ($label = $param->get('label')) {
                if (is_string($label)) {
                    $label = ['content' => $label];
                } elseif (is_bool($label)) {
                    $label = [];
                }

                $param->set('label', array_merge([
                    'tag'      => 'label',
                    'attrs'    => [],
                    'wrapper'  => false,
                    'position' => 'before',
                    'require'  => true,
                ], is_array($label) ? $label : []));

                if (!$param->has('label.attrs.id')) {
                    $param->set('label.attrs.id', "FormField-label--{$this->getSlug()}_{$this->form()->getIndex()}");
                }

                if (!$param->get('label.attrs.id')) {
                    $param->pull('label.attrs.id');
                }

                $default_class = "%s FormField-label FormField-label--{$this->getType()} FormField-label--{$this->getSlug()}";
                if (!$param->has('label.attrs.class')) {
                    $param->set('label.attrs.class', $default_class);
                } else {
                    $param->set('label.attrs.class', sprintf($param->get('label.attrs.class'), $default_class));
                }

                if (!$param->get('label.attrs.class')) {
                    $param->pull('label.attrs.class');
                }

                if ($for = $param->get('attrs.id')) {
                    $param->set('label.attrs.for', $for);
                }

                if (!$param->has('label.content')) {
                    $param->set('label.content', $this->getTitle());
                }

                if (!$param->get('label.content')) {
                    $param->pull('label.content');
                }

                if (($require = $param->pull('label.require')) && $param->get('required.tagged')) {
                    $content = $param->get('label.content');

                    $param->set('label.content', $content . $this->form()->view('field-required', ['field' => $this]));

                    $param->forget('required.tagged');
                }

                if ($param->get('label.wrapper')) {
                    $param->set('label.wrapper', [
                        'tag'   => 'div',
                        'attrs' => [
                            'id'    => "FormField-labelWrapper--{$this->getSlug()}_{$this->form()->getIndex()}",
                            'class' => "FormField-labelWrapper FormField-labelWrapper--{$this->getType()}" .
                                " FormField-labelWrapper--{$this->getSlug()}",
                        ],
                    ]);
                }
            }

            $this->renderable = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $args = array_merge($this->getExtras(), [
            'name'  => $this->getName(),
            'attrs' => $this->params('attrs', []),
        ]);

        if ($this->supports('choices')) {
            $args['choices'] = $this->params('choices', []);
        }

        $args['value'] = $this->getValue();

        return Field::get($this->getType(), $args)->render();
    }

    /**
     * @inheritDoc
     */
    public function resetValue(): FieldDriverContract
    {
       $this->params(['value' => $this->default]);

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): FieldDriverContract
    {
        if ($this->alias === null) {
            $this->alias = $alias;
        }

        return $this;
    }

    public function setDefault($default): FieldDriverContract
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setExtra(string $key, $value): FieldDriverContract
    {
        return $this->params(["extras.{$key}" => $value]);
    }

    /**
     * @inheritdoc
     */
    public function setPosition(int $position = 0): FieldDriverContract
    {
        $this->params(['position' => $position]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSessionValue(): FieldDriverContract
    {
        if ($this->form()->supports('session') && $this->supports('session')) {
            $value = $this->form()->session()->get("request.{$this->getName()}");

            if (!is_null($value)) {
                $this->setValue($value);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSlug(string $slug): FieldDriverContract
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value): FieldDriverContract
    {
        $this->form()->event('field.set.value', [&$value, $this]);

        $this->params(['value' => $value]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $support): bool
    {
        return in_array($support, $this->getSupports());
    }

    /**
     * @inheritDoc
     */
    public function validate(): void
    {
        $check = true;

        $this->form()->event('field.validate.' . $this->getType(), [&$this]);
        $this->form()->event('field.validate', [&$this]);

        if ($this->getRequired('check')) {
            $value = $this->getValue($this->getRequired('raw', true));

            if (!$check = $this->form()->validate()->call(
                $this->getRequired('call'), $value,
                $this->getRequired('args', []))
            ) {
                 throw (new FieldValidateException(sprintf($this->getRequired('message'), $this->getTitle())))
                    ->setField($this)->setAlias('_required');
            }
        }

        if ($check) {
            if ($validations = $this->params('validations', [])) {
                $value = $this->getValue($this->getRequired('raw', true));

                foreach ($validations as $i => $validation) {
                    if (!$this->form()->validate()->call($validation['call'], $value, $validation['args'])) {
                        if (!$alias = $validation['alias'] ?: null) {
                            $alias = (is_string($validation['call'])) ? $validation['call'] : $i;
                        }
                        throw (new FieldValidateException(sprintf($validation['message'], $this->getTitle())))
                            ->setField($this)->setAlias($alias);
                    }
                }
            }
        }

        $this->form()->event('field.validated.' . $this->getType(), [&$this]);
        $this->form()->event('field.validated', [&$this]);
    }
}