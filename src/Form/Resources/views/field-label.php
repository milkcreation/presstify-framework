<?php
/**
 * Etiquette de champ de formulaire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FactoryView $this
 * @var tiFy\Contracts\Form\FactoryField $field
 */
?>

<?php if ($field->hasLabel()) : ?>
    <?php
    echo partial(
        'tag',
        array_merge(
            [
                'after' => $this->fetch('field-tag', compact('field'))
            ],
            $field->get('label', [])
        )
    );
    ?>
<?php endif; ?>