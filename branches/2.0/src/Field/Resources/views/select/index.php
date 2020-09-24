<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>
<?php if ($this->get('wrapper')) : ?>
    <?php $this->layout('wrapper', $this->all()); ?>
<?php endif; ?>

<?php $this->before(); ?>
    <select <?php $this->attrs(); ?>>
        <?php echo $this->get('choices', ''); ?>
    </select>
<?php $this->after(); ?>