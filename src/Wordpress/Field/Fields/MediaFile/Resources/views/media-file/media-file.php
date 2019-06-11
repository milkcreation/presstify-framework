<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>
<?php $this->before(); ?>

    <div <?php $this->attrs(); ?>>
        <?php
        echo field(
            'hidden',
            [
                'name'  => $this->get('name'),
                'value' => $this->get('value'),
                'attrs' => [
                    'aria-control' => 'input',
                ],
            ]
        );
        ?>

        <div class="tiFyField-mediaFileInput tiFy-Input--media">
            <?php
            echo field(
                'text',
                [
                    'value' => $this->get('selected_infos', ''),
                    'attrs' => [
                        'autocomplete' => 'off',
                        'disabled',
                        'aria-control' => 'infos',
                        'placeholder'  => __('Cliquez pour ajouter un fichier', 'tify'),
                    ],
                ]
            );
            ?>
        </div>

        <?php
        echo partial(
            'tag',
            [
                'tag'     => 'a',
                'attrs'   => [
                    'class'        => 'dashicons dashicons-no-alt',
                    'href'         => '#' . $this->get('attrs.id'),
                    'aria-control' => 'reset',
                ],
                'content' => '',
            ]
        );
        ?>
    </div>

<?php $this->after();