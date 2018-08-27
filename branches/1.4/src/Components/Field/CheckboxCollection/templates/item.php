<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<li class="tiFyField-CheckboxCollectionItem">
    <?php
        tify_field_checkbox($this->get('checkbox', []));
    ?>
    <?php
        tify_field_label($this->get('label', []));
    ?>
</li>