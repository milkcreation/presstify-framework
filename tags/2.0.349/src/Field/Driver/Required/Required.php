<?php declare(strict_types=1);

namespace tiFy\Field\Driver\Required;

use tiFy\Contracts\Field\Required as RequiredContract;
use tiFy\Field\FieldDriver;

class Required extends FieldDriver implements RequiredContract
{
    /**
     * {@inheritDoc}
     *
     * @return array {
     * @var array $attrs Attributs HTML du champ.
     * @var string $after Contenu placé après le champ.
     * @var string $before Contenu placé avant le champ.
     * @var string $name Clé d'indice de la valeur de soumission du champ.
     * @var string $content
     * @var array $viewer Liste des attributs de configuration du pilote d'affichage.
     * }
     */
    public function defaults(): array
    {
        return [
            'attrs'   => [],
            'after'   => '',
            'before'  => '',
            'name'    => '',
            'content' => '*',
            'viewer'  => [],
        ];
    }
}