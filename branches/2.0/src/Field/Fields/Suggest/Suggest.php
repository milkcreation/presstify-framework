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
            'container' => [],
            'options'   => [
                'minLength' => 2,
            ],
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
        $default_class = 'FieldSuggest-input FieldSuggest-input' . '--' . $this->getIndex();
        if (!$this->has('attrs.class')) {
            $this->set('attrs.class', $default_class);
        } else {
            $this->set('attrs.class', sprintf($this->get('attrs.class', ''), $default_class));
        }

        $this->parseValue();
        $this->parseViewer();

        $this->set('options.classes', array_merge([
            'picker'      => 'FieldSuggest-picker',
            'picker-item' => 'FieldSuggest-pickerItem',
        ], $this->get('options.classes', [])));


        $options = [
            'autocomplete' => $this->get('options', []),
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

        $this->set('attrs.data-control', 'suggest.input');

        $container_class = 'FieldSuggest FieldSuggest--' . $this->getIndex();
        if (!$this->has('container.attrs.class')) {
            $this->set('container.attrs.class', $container_class);
        } else {
            $this->set('container.attrs.class', sprintf($this->get('container.attrs.class', ''), $container_class));
        }

        $this->set('container', array_merge([
            'tag'     => 'span',
            'content' => $this->viewer('input', $this->all()),
        ], $this->get('container', [])));

        $this->set([
            'container.attrs.data-control' => 'suggest',
            'container.attrs.data-options' => $options,
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUrl(?string $url =  null): FieldFactoryContract
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
            ->filter(function ($value) {
                return preg_match('/' . req::input('_term', '') . '/i', $value);
            })->map(function (&$value, $key) {
                return [
                    'label'  => (string)$this->viewer('label', compact('value')),
                    'value'  => (string)$key,
                    'render' => (string)$this->viewer('render', compact('value')),
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