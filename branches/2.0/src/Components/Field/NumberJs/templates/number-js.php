<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

<div <?php echo $this->htmlAttrs($this->get('container.attrs')); ?>>
    <?php
        tify_partial_tag(
            [
                'tag'   => 'input',
                'attrs' => $this->get('attrs', [])
            ]
        );
    ?>
</div>


<?php $this->after(); ?>