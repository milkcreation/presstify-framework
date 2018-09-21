<?php
/**
 * @var tiFy\App\Templates\AppTemplateController $this
 */
?>

<?php foreach ($this->get('taxonomy', []) as $tax) : ?>
    <?php echo field('hidden', ['name' => "tax_input[{$tax}][]", 'value' => '']); ?>
<?php endforeach; ?>

<?php if ($this->get('multiple', true)) : ?>
    <?php
    echo field(
        'checkbox-collection',
        [
            'items' => $this->get('items', []),
        ]
    );
    ?>
<?php else : ?>
    <?php
    echo field(
        'radio-collection',
        [
            'items' => $this->get('items', []),
        ]
    );
    ?>
<?php endif; ?>