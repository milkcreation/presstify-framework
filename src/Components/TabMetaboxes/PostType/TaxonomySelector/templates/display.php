<?php
/**
 * @var tiFy\Apps\Templates\AppTemplateController $this
 */
?>

<?php foreach ($this->get('taxonomy', []) as $tax) : ?>
    <?php tify_field_hidden(['name' => "tax_input[{$tax}][]", 'value' => '']); ?>
<?php endforeach; ?>

<?php if ($this->get('multiple', true)) : ?>
    <?php
    tify_field_checkbox_collection(
        [
            'items' => $this->get('items', []),
        ]
    );
    ?>
<?php else : ?>
    <?php
    tify_field_radio_collection(
        [
            'items' => $this->get('items', []),
        ]
    );
    ?>
<?php endif; ?>