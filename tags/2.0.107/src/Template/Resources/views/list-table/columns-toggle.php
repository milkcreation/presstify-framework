<?php
/**
 * Interface de bascule d'affichage des colonnes de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\ListTable\Viewer\Viewer $this
 * @var tiFy\Template\Templates\ListTable\Contracts\ColumnsCollection $cols
 * @var tiFy\Template\Templates\ListTable\Contracts\ColumnsItem $col
 */
?>
<?php foreach ($this->columns() as $col) {
    echo field('checkbox', [
        'after' => (string)field('label', [
            'content' => $col->getTitle(),
            'attrs'   => [
                'for' => 'ListTable-columnToggle--' . $col->getName()
            ]
        ]),
        'value' => $col->getName(),
        'attrs' => [
            'id'           => 'ListTable-columnToggle--' . $col->getName(),
            'data-control' => 'list-table.column.toggle'
        ],
        'checked' => true
    ]);
}