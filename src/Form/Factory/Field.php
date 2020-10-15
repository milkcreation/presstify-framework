<?php

namespace tiFy\Form\Factory;

use Closure;
use Illuminate\Support\Arr;
use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FactoryGroup;
use tiFy\Contracts\Form\FieldController;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Support\ParamsBag;

class Field extends ParamsBag implements FactoryField
{
    use ResolverTrait;

    /**
     * Liste des attributs de configuration.
     * @var array  {
     *
     * @var string $title Intitulé de qualification. Valeur par défaut. ex. label.
     * @var string $before Contenu HTML affiché avant le champ.
     * @var string $after Contenu HTML affiché après le champ.
     * @var bool|string|array $wrapper Affichage de l'encapuleur de champ. false si masqué|true charge les attributs par
     *                                 défaut|array permet de définir des attributs personnalisés.
     * @var bool|string|array $label Affichage de l'intitulé de champ. false si masqué|true charge les attributs par
     *                               défaut|array permet de définir des attributs personnalisés.
     * @var int $group Indice du groupe d'appartenance.
     * @var int $position Ordre d'affichage général ou dans le groupe s'il est défini.
     * @var string $type Type de champ.
     * @var string $name Indice de qualification de la variable de requête.
     * @var mixed $value Valeur courante de la variable de requête.
     * @var array $choices Liste de choix des valeurs multiples.
     * @var array $attrs Liste des attributs HTML. (hors name & value)
     * @var array $grid Attributs d'agencement du champ. La propriété doit être active au niveau du formulaire.
     * @var array $extras Liste des attributs complémentaires de configuration.
     * @var array $supports Définition des propriétés de support. label|wrapper|request|tabindex|transport.
     * @var boolean|string|array $required Configuration de champs requis. false si désactivé|true charge les attributs
     *                                     par défaut|array
     * {
     * @var boolean|string|array $tagged Affichage de l'indicateur de champ requis. false si masqué|true charge
     *                                        les attributs par défaut|string valeur de l'indicateur|array permet de
     *                                        définir des attributs personnalisés.
     * @var boolean $check Activation du test d'existance natif.
     * @var mixed $value_none Valeur à comparer pour le test d'existance.
     * @var string|callable $call Fonction de validation ou alias de qualification.
     * @var array $args Liste des variables passées en argument dans la fonction de validation.
     * @var boolean $raw Activation du format brut de la valeur.
     * @var string $message Message de notification de retour en cas d'erreur.
     * }
     * @var null|boolean $transport Court-circuitage de la propriété de support du transport des données à
     *                              l'issue de la soumission.
     * @var null|boolean $session Court-circuitage de la propriété de support du stockage en session des données à
     *                              l'issue de la soumission.
     * @var array $validations {
     *      Liste des fonctions de validation d'intégrité du champ lors de la soumission.
     *
     * @var string|callable $call Fonction de validation ou alias de qualification.
     * @var array $args Liste des variables passées en arguments dans la fonction de validation.
     * @var string $message Message de notification d'erreur.
     * @var boolean $raw Activation du format brut de la valeur.
     * }
     * @var array $addons Liste des attributs de configuration associés aux addons.
     */
    protected $attributes = [
        'addons'      => [],
        'after'       => '',
        'attrs'       => [],
        'before'      => '',
        'choices'     => [],
        'extras'      => [],
        'grid'        => [],
        'group'       => 0,
        'label'       => true,
        'name'        => '',
        'position'    => 0,
        'required'    => false,
        'supports'    => [],
        'wrapper'     => null,
        'title'       => '',
        'transport'   => null,
        'type'        => 'html',
        'validations' => [],
        'value'       => ''
    ];

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
     * CONSTRUCTEUR.
     *
     * @param string $slug Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param FormFactory $form
     *
     * @return void
     */
    public function __construct(string $slug, array $attrs, FormFactory $form)
    {
        $this->slug = $slug;
        $this->form = $form;

        $this->set($attrs)->parse();

        $this->events('field.init.' . $this->getSlug(), [&$this]);
        $this->events('field.init', [&$this]);
    }

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
    public function addError(string $message, array $data = []): FactoryField
    {
        $this->notices()->add('error', $message, array_merge($data, [
            'field' => $this->getSlug()
        ]));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function after(): string
    {
        $after = $this->get('after', '');
        return $after instanceof Closure ? call_user_func($after) : strval($after);
    }

    /**
     * @inheritDoc
     */
    public function before(): string
    {
        $before = $this->get('before', '');
        return $before instanceof Closure ? call_user_func($before) : strval($before);
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'name'  => $this->slug,
            'title' => $this->slug
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAddonOption(string $name, ?string $key = null, $default = null)
    {
        return (is_null($key))
            ? $this->get("addons.{$name}", [])
            : $this->get("addons.{$name}.{$key}", $default);
    }

    /**
     * @inheritDoc
     */
    public function getController(): FieldController
    {
        return $this->resolve("field.{$this->getType()}.{$this->form()->name()}.{$this->getSlug()}");
    }

    /**
     * @inheritDoc
     */
    public function getExtras(?string $key = null, $default = null)
    {
        return (is_null($key)) ? $this->get('extras', []) : $this->get("extras.{$key}", $default);
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): ?FactoryGroup
    {
        return $this->fromGroup($this->get('group', ''));
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->get('name', '');
    }

    /**
     * @inheritDoc
     */
    public function getPosition(): int
    {
        return (int)$this->get('position', 0);
    }

    /**
     * @inheritDoc
     */
    public function getRequired(?string $key = null, $default = null)
    {
        return $this->get('required' . ($key ? ".{$key}" : ''), $default);
    }

    /**
     * @inheritDoc
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return (string)$this->get('title', '');
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return (string)$this->get('type');
    }

    /**
     * @inheritDoc
     */
    public function getValue(bool $raw = true)
    {
        $value = $this->get('value');

        $this->form()->events('field.get.value', [&$value, $this]);

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

        if ($choices = $this->get('choices', [])) {
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
        return $this->supports('label') && !empty($this->get('label'));
    }

    /**
     * @inheritDoc
     */
    public function hasWrapper(): bool
    {
        return $this->supports('wrapper') && !empty($this->get('wrapper'));
    }

    /**
     * @inheritDoc
     */
    public function onError(): bool
    {
        return ($this->supports('request') && !empty($this->notices()->query('error', ['field' => $this->getSlug()])))
            || !!$this->error;
    }

    /**
     * @inheritDoc
     */
    public function parseValidations($validations, array $results = []): array
    {
        if (is_array($validations)) {
            if (isset($validations['call'])) {
                $results[] = array_merge(
                    [
                        'call'    => '__return_true',
                        'args'    => [],
                        'message' => __('Le format du champ "%s" est invalide', 'tify'),
                        'raw'     => false
                    ],
                    $validations
                );
            } else {
                foreach ($validations as $validation) {
                    $results += $this->parseValidations($validation, $results);
                }
            }
        } elseif(is_string($validations)) {
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
    public function prepare(): FactoryField
    {
        $this->events('field.prepare.' . $this->getType(), [&$this]);
        $this->events('field.prepare', [&$this]);

        // Nom de qualification d'enregistrement de la requête.
        $name = $this->get('name', '');
        if (!is_null($name)) :
            $this->set('name', $name ? esc_attr($name) : esc_attr($this->getSlug()));
        endif;

        // Valeur par défaut.
        $this->default = $this->get('value', null);

        /**
         * Initialisation du controleur de champ.
         * @var FieldController $control
         */
        $control = (app()->has("form.field.{$this->getType()}"))
            ? $this->resolve("field.{$this->getType()}", [$name, $this])
            : $this->resolve("field", [$name, $this]);

        app()->share("form.field.{$this->getType()}.{$this->form()->name()}.{$this->getSlug()}", $control);

        // Propriétés de support.
        if (!$this->get('supports')) {
            $this->set('supports', $control->supports());
        }

        $transport = $this->get('transport');
        if ($transport && !in_array('transport', $this->get('supports', []))) {
            $this->push('supports', 'transport');
        } elseif ($transport === false) {
            $this->set('supports', array_diff($this->get('supports', []), ['transport']));
        }

        $session = $this->get('session');
        if ($session && !in_array('session', $this->get('supports', []))) {
            $this->push('supports', 'session');
        } elseif ($session === false) {
            $this->set('supports', array_diff($this->get('supports', []), ['session']));
        }

        $this->setSessionValue();

        if ($this->get('wrapper')) :
            $this->push('supports', 'wrapper');
        elseif (in_array('wrapper', $this->get('supports', []))) :
            $this->set('wrapper', true);
        endif;

        // Attributs de champ requis (marqueur et fonction de traitement).
        if ($required = $this->get('required', false)) :
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
                    'content' => '*'
                ], $tagged);
            }

            $required['call'] = !empty($required['value_none']) && empty($required['call']) ? '!equals' : 'notEmpty';
            $required['args'] = !empty($required['value_none']) && empty($required['args'])
                ? [] + [$required['value_none']]
                : [];

            $this->set('required', $required);
        endif;

        // Liste des tests de validation.
        if ($validations = $this->get('validations')) {
            $this->set('validations', $this->parseValidations($validations));
        }

        foreach ($this->addons() as $name => $addon) {
            $this->set(
                "addons.{$name}",
                array_merge($addon->defaultsFieldOptions(), $this->get("addons.{$name}", []) ? : [])
            );
        }

        $this->events('field.prepared.' . $this->getType(), [&$this]);
        $this->events('field.prepared', [&$this]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resetValue(): FactoryField
    {
        return $this->set('value', $this->default);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return (string)$this->getController();
    }

    /**
     * @inheritDoc
     */
    public function renderPrepare(): FactoryField
    {
        if (!$this->has('attrs.id')) {
            $this->set('attrs.id', "Form{$this->form()->index()}-fieldInput--{$this->getSlug()}");
        }

        if (!$this->get('attrs.id')) {
            $this->pull('attrs.id');
        }

        $default_class = "%s Form-fieldInput Form-fieldInput--{$this->getType()} Form-fieldInput--{$this->getSlug()}";

        if (!$this->has('attrs.class')) {
            $this->set('attrs.class', $default_class);
        } else {
            $this->set('attrs.class', sprintf($this->get('attrs.class', ''), $default_class));
        }

        if (!$this->get('attrs.class')) {
            $this->pull('attrs.class');
        }

        if (!$this->has('attrs.tabindex')) {
            $this->set('attrs.tabindex', $this->getPosition());
        }

        if ($this->get('attrs.tabindex') === false) :
            $this->pull('attrs.tabindex');
        endif;

        if ($this->onError()) :
            $this->set('attrs.aria-error', 'true');
        endif;

        // Attributs HTML de l'encapsuleur de champ.
        if ($wrapper = $this->get('wrapper')) :
            $wrapper = (is_array($wrapper)) ? $wrapper : [];
            $this->set('wrapper', array_merge(['tag' => 'div', 'attrs' => []], $wrapper));

            if (!$this->has('wrapper.attrs.id')) :
                $this->set('wrapper.attrs.id', "Form{$this->form()->index()}-field--{$this->getSlug()}");
            endif;
            if (!$this->get('wrapper.attrs.id')) :
                $this->pull('wrapper.attrs.id');
            endif;

            $default_class = "Form-field Form-field--{$this->getType()} Form-field--{$this->getSlug()}";
            if (!$this->has('wrapper.attrs.class')) :
                $this->set('wrapper.attrs.class', $default_class);
            else :
                $this->set('wrapper.attrs.class', sprintf($this->get('wrapper.attrs.class', ''), $default_class));
            endif;
            if (!$this->get('wrapper.attrs.class')) :
                $this->pull('wrapper.attrs.class');
            endif;
        endif;

        // Activation de l'agencement des éléments.
        if ($this->form()->hasGrid()) :
            $grid = $this->get('grid', []);
            $prefix = $this->hasWrapper() ? 'wrapper.' : '';

            $grid = is_array($grid) ? $grid : [];
            $grid = array_merge(
                $this->form()->get('grid.defaults', []),
                $grid
            );

            foreach ($grid as $k => $v) :
                $this->set("{$prefix}attrs.data-grid_{$k}", filter_var($v, FILTER_SANITIZE_STRING));
            endforeach;
        endif;

        if ($label = $this->get('label')) {
            if (is_string($label)) {
                $label = ['content' => $label];
            } elseif (is_bool($label)) {
                $label = [];
            }

            $this->set('label', array_merge([
                'tag'      => 'label',
                'attrs'    => [],
                'wrapper'  => false,
                'position' => 'before'
            ], is_array($label) ? $label : []));

            if (!$this->has('label.attrs.id')) {
                $this->set('label.attrs.id', "Form{$this->form()->index()}-fieldLabel--{$this->getSlug()}");
            }

            if (!$this->get('label.attrs.id')) {
                $this->pull('label.attrs.id');
            }

            $default_class = "%s Form-fieldLabel Form-fieldLabel--{$this->getType()} Form-fieldLabel--{$this->getSlug()}";
            if (!$this->has('label.attrs.class')) {
                $this->set('label.attrs.class', $default_class);
            } else {
                $this->set('label.attrs.class', sprintf($this->get('label.attrs.class', ''), $default_class));
            }

            if (!$this->get('label.attrs.class')) {
                $this->pull('label.attrs.class');
            }

            if ($for = $this->get('attrs.id')) {
                $this->set('label.attrs.for', $for);
            }

            if (!$this->has('label.content')) {
                $this->set('label.content', $this->getTitle());
            }

            if (!$this->get('label.content')) {
                $this->pull('label.content');
            }

            if ($this->get('label.wrapper')) {
                $this->set('label.wrapper', [
                    'tag'   => 'div',
                    'attrs' => [
                        'id'    => "Form{$this->form()->index()}-fieldLabelWrapper--{$this->getSlug()}",
                        'class' => "Form-fieldLabelWrapper Form-fieldLabelWrapper--{$this->getType()}" .
                            " Form-fieldLabelWrapper--{$this->getSlug()}"
                    ]
                ]);
            }
        }

        if ($this->get('required.tagged')) :
            if (!$this->has('required.tagged.attrs.id')) :
                $this->set('required.tagged.attrs.id', "Form{$this->form()->index()}-fieldRequired--{$this->getSlug()}");
            endif;
            if (!$this->get('required.tagged.attrs.id')) :
                $this->pull('required.tagged.attrs.id');
            endif;

            $default_class = "%s Form-fieldRequired Form-fieldRequired--{$this->getType()} Form-fieldRequired--{$this->getSlug()}";
            if (!$this->has('required.tagged.attrs.class')) :
                $this->set('required.tagged.attrs.class', $default_class);
            else :
                $this->set(
                    'required.tagged.attrs.class',
                    sprintf($this->get('required.tagged.attrs.class', ''), $default_class)
                );
            endif;
            if (!$this->get('required.tagged.attrs.class')) :
                $this->pull('required.tagged.attrs.class');
            endif;
        endif;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setExtra(string $key, $value): FactoryField
    {
        return $this->set("extras.{$key}", $value);
    }

    /**
     * @inheritDoc
     */
    public function setOnError(bool $status = true): FactoryField
    {
       $this->error = $status;

       return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPosition(int $position = 0): FactoryField
    {
        $this->set('position', $position);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSessionValue(): FactoryField
    {
        if ($this->supports('session')) {
            $value = $this->form()->session()->get($this->getName());

            if (!is_null($value)) {
                $this->setValue($value);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value): FactoryField
    {
        $this->events('field.set.value', [&$value, $this]);

        return $this->set('value', $value);
    }

    /**
     * @inheritDoc
     */
    public function supports(?string $support = null)
    {
        return is_null($support) ? $this->get('supports', []) : in_array($support, $this->get('supports', []));
    }
}