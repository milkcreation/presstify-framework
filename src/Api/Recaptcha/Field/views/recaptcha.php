<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<?php $this->before(); ?>
    <?php
    echo field(
        'hidden',
        [
            'name' => $this->getName(),
            'value' => -1
        ]
    );
    ?>

    <?php
    echo partial(
        'tag',
        [
            'tag' => 'div',
            'attrs' => $this->get('attrs', [])
        ]
    )
    ?>

<?php $this->after(); ?>