<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<?php $this->before(); ?>

    <div <?php echo $this->getHtmlAttrs($this->get('container.attrs', [])); ?>>
        <?php $this->insert('input', $this->all()); ?>

        <?php $this->insert('infos', $this->all()); ?>
    </div>

<?php $this->after();