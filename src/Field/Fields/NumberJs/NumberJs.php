<?php declare(strict_types=1);

namespace tiFy\Field\Fields\NumberJs;

use tiFy\Contracts\Field\{FieldFactory as FieldFactoryContract, NumberJs as NumberJsContract};
use tiFy\Field\FieldFactory;

class NumberJs extends FieldFactory implements NumberJsContract
{
    /**
     * {@inheritDoc}
     *
     * @return array {
     *      @var array $attrs Attributs HTML du champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $before Contenu placé avant le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var string $value Valeur courante de soumission du champ.
     *      @var array $viewer Liste des attributs de configuration du pilote d'affichage.
     *      @var string $container Liste des attribut de configuration du conteneur de champ
     *      @var array $options {
     *          Liste des options du contrôleur ajax.
     *          @see http://api.jqueryui.com/spinner/
     *      }
     * }
     */
    public function defaults(): array
    {
        return [
            'attrs'  => [],
            'after'  => '',
            'before' => '',
            'name'   => '',
            'value'  => 0,
            'viewer' => [],
            'container' => [],
            'options'   => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function parse(): FieldFactoryContract
    {
        $this->set('container.attrs.id', 'tiFyField-NumberJsContainer--' . $this->getIndex());

        parent::parse();

        if ($container_class = $this->get('container.attrs.class')) {
            $this->set('container.attrs.class', "tiFyField-NumberJsContainer {$container_class}");
        } else {
            $this->set('container.attrs.class', 'tiFyField-NumberJsContainer');
        }

        if (!$this->has('attrs.id')) {
            $this->set('attrs.id', 'tiFyField-NumberJs--' . $this->getIndex());
        }
        $this->set('attrs.type', 'text');
        $this->set('attrs.data-options', array_merge([
            'icons' => [
                'down' => 'dashicons dashicons-arrow-down-alt2',
                'up'   => 'dashicons dashicons-arrow-up-alt2',
            ]
        ],$this->get('options', [])));
        $this->set('attrs.data-control', 'number-js');

        return $this;
    }
}