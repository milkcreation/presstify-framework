<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<li <?php $this->attrs(); ?>>
    <?php
        tify_field_radio($this->get('radio', []));
    ?>
    <?php
        tify_field_label($this->get('label', []));
    ?>
</li>