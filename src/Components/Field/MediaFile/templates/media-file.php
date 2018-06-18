<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

    <div <?php $this->attrs(); ?>>
        <?php
        echo tify_field_hidden(
            [
                'name'  => $this->get('name'),
                'value' => $this->get('value'),
                'attrs' => [
                    'aria-control' => 'input',
                ],
            ]
        );
        ?>

        <div class="tiFy-Input--media">
            <?php
            echo tify_field_text(
                [
                    'value'        => $this->get('selected_infos', ''),
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
        echo tify_partial_tag(
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

<?php $this->after(); ?>