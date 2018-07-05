<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<li <?php $this->attrs(); ?>>
    <?php
        tify_field_checkbox($this->get('checkbox', []));
    ?>
    <?php
        tify_field_label($this->get('label', []));
    ?>
</li>