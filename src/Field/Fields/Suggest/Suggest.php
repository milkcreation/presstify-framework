<?php declare(strict_types=1);

namespace tiFy\Field\Fields\Suggest;

use tiFy\Contracts\Field\FieldFactory as FieldFactoryContract;
use tiFy\Contracts\Field\Suggest as SuggestContract;
use tiFy\Field\FieldFactory;
use tiFy\Support\Proxy\{Request as req, Router as route};

class Suggest extends FieldFactory implements SuggestContract
{
    /**
     * Jeu de données d'exemple.
     * @var string[]
     */
    protected $languages = [
        "ActionScript",
        "AppleScript",
        "Asp",
        "BASIC",
        "C",
        "C++",
        "Clojure",
        "COBOL",
        "ColdFusion",
        "Erlang",
        "Fortran",
        "Groovy",
        "Haskell",
        "Java",
        "JavaScript",
        "Lisp",
        "Perl",
        "PHP",
        "Python",
        "Ruby",
        "Scala",
        "Scheme",
    ];

    /**
     * Url de traitement.
     * @var string Url de traitement
     */
    protected $url;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->setUrl();
    }

    /**
     * {@inheritDoc}
     *
     * @return array {
     * @var array $attrs Attributs HTML du champ.
     * @var string $after Contenu placé après le champ.
     * @var string $before Contenu placé avant le champ.
     * @var string $name Clé d'indice de la valeur de soumission du champ.
     * @var string $value Valeur courante de soumission du champ.
     * @var array $viewer Liste des attributs de configuration du pilote d'affichage.
     * @var array|bool $ajax Liste des attributs de recherche des éléments via une requête xhr.
     * @var bool|array $alt Activation du champ alternatif de stockage du résultat de la recherche|attributs de
     *      configuration du champ altérnatif. @see \tiFy\Field\Fields\Hidden\Hidden
     * @see https://api.jquery.com/jquery.ajax/
     * @var array $options Liste des attributs de configuration de l'autocomplétion.
     * @see https://api.jqueryui.com/autocomplete/
     * }
     */
    public function defaults(): array
    {
        return [
            'attrs'     => [],
            'after'     => '',
            'before'    => '',
            'name'      => '',
            'value'     => '',
            'viewer'    => [],
            'ajax'      => false,
            'alt'       => false,
            'classes'   => [],
            'options'   => [
                'minLength' => 2,
            ],
            'spinner'   => true,
            'reset'     => true
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function parse(): FieldFactoryContract
    {
        parent::parse();

        $defaultClasses = [
            'alt'     => 'FieldSuggest-alt',
            'input'   => 'FieldSuggest-input',
            'item'    => 'FieldSuggest-pickerItem',
            'items'   => 'FieldSuggest-picker',
            'reset'   => 'FieldSuggest-reset ThemeButton--close',
            'spinner' => 'FieldSuggest-spinner ThemeSpinner',
            'wrap'    => 'FieldSuggest-wrap',
        ];
        foreach ($defaultClasses as $k => $v) {
            $this->set("classes.{$k}", sprintf($this->get("classes.{$k}", '%s'), $v));
        }

        $options = [
            'alt'          => $this->get('alt'),
            'autocomplete' => $this->get('options', []),
            'classes'      => $this->get('classes', []),
            'reset'        => $this->get('reset'),
            'spinner'      => $this->get('spinner'),
        ];

        if ($ajax = $this->get('ajax')) {
            $defaults = [
                'url'  => $this->getUrl(),
                'type' => 'post',
                'data' => [],
            ];
            $options['ajax'] = is_array($ajax) ? array_merge($defaults, $ajax) : $defaults;
        } elseif (!$this->has('autocomplete.source')) {
            $options['autocomplete']['source'] = $this->languages;
        }

        $this->set([
            'attrs.data-control'           => $this->get('attrs.data-control', 'suggest'),
            'attrs.data-options'           => $options
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseDefaults(): FieldFactoryContract
    {
        $default_class = 'FieldSuggest-input FieldSuggest-input' . '--' . $this->getIndex();
        if (!$this->has('attrs.class')) {
            $this->set('attrs.class', $default_class);
        } else {
            $this->set('attrs.class', sprintf($this->get('attrs.class', ''), $default_class));
        }

        $this->parseName();
        $this->parseValue();
        $this->parseViewer();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUrl(?string $url = null): FieldFactoryContract
    {
        $this->url = is_null($url) ? route::xhr(md5($this->getAlias()), [$this, 'xhrResponse'])->getUrl() : $url;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): array
    {
        $items = collect($this->languages)
            ->filter(function ($label) {
                return preg_match('/' . req::input('_term', '') . '/i', $label);
            })->map(function (&$label, $value) {
                return [
                    'alt'   => (string)$value,
                    'label' => (string)$this->viewer('item-label', compact('label', 'value')),
                    'value' => (string)$value,
                ];
            })->all();

        return [
            'success' => true,
            'data'    => [
                'items' => $items,
            ],
        ];
    }
}