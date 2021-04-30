<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use Illuminate\Support\Collection;
use tiFy\Field\FieldDriver;
use tiFy\Support\Proxy\Request;

class SuggestDriver extends FieldDriver implements SuggestDriverInterface
{
    /**
     * Jeu de données d'exemple.
     * @var string[]
     */
    protected $sample = [
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
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                /**
                 * @var array|bool $ajax Liste des attributs de recherche des éléments via une requête xhr.
                 * @see https://api.jquery.com/jquery.ajax/
                 */
                'ajax'    => false,
                /**
                 * @var bool|string|array $alt Activation du champ alternatif de stockage du résultat de la recherche|attributs de configuration du champ alternatif.
                 * @see \tiFy\Field\Drivers\HiddenDriver
                 */
                'alt'     => false,
                /**
                 *
                 */
                'classes' => [],
                /**
                 * @var array $options Liste des attributs de configuration de l'autocomplétion.
                 * @see https://api.jqueryui.com/autocomplete/
                 */
                'options' => [
                    'minLength' => 2,
                ],
                /**
                 *
                 */
                'spinner' => true,
                /**
                 *
                 */
                'reset'   => true,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
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
                'url'      => $this->getXhrUrl(),
                'type'     => 'post',
                'data'     => [],
                'dataType' => 'json',
                'timeout'  => 5000,
            ];
            $options['ajax'] = is_array($ajax) ? array_merge($defaults, $ajax) : $defaults;
        } elseif (!$this->has('autocomplete.source')) {
            $options['autocomplete']['source'] = $this->sample;
        }

        $this->set(
            [
                'attrs.data-control' => $this->get('attrs.data-control', 'suggest'),
                'attrs.data-options' => $options,
            ]
        );
        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->fieldManager()->resources('/views/suggest');
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): array
    {
        $items = (new Collection($this->sample))
            ->filter(
                function ($label) {
                    return preg_match('/' . Request::input('_term', '') . '/i', $label);
                }
            )->map(
                function ($label, $value) {
                    return [
                        'alt'   => (string)$value,
                        'label' => (string)$this->view('item-label', compact('label', 'value')),
                        'value' => (string)$value,
                    ];
                }
            )->all();

        return [
            'success' => true,
            'data'    => [
                'items' => $items,
            ],
        ];
    }
}