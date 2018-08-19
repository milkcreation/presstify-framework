<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<li class="tiFyField-RadioCollectionItem">
    <?php
        tify_field_radio($this->get('radio', []));
    ?>
    <?php
        tify_field_label($this->get('label', []));
    ?>
</li>