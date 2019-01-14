<?php
/**
 * Entête de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\Viewer\Viewer $this
 * @var string $attrs Liste des attributs de blise HTML.
 * @var int $index Numero d'instance d'affichage
 */
?>
<td <?php echo $this->get('attrs', ''); ?>>
    <?php
    echo field(
            'label',
            [
                'attrs' => [
                    'class' => 'screen-reader-text',
                    'for'   => 'cb-select-all-' . $this->get('index')
                ],
                'content' => __( 'Select All' )
            ]
        );
    ?>
    <?php
    echo field(
        'checkbox',
        [
            'attrs' => [
                'id' => 'cb-select-all-' . $this->get('index')
            ]
        ]
    );
    ?>
</td>
