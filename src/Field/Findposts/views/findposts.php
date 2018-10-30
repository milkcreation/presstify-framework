<?php
/**
 * Field Findposts.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Field\FieldView $this
 */
?>

<div class="tiFy-Input--search">
<?php
echo field(
    'text',
    [
        'attrs' => $this->get('attrs', [])
    ]
);
?>
</div>