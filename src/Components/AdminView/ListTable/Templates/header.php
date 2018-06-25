<?php
/**
 * Entête.
 *
 * @var tiFy\Components\AdminView\ListTable\TemplateController $this
 */
?>

<h2>
    <?php echo $this->params()->get('page_title'); ?>

    <?php /*if($edit_base_uri = $this->param('edit_base_uri')) : ?>
        <a class="add-new-h2" href="<?php echo $edit_base_uri;?>"><?php echo $this->getLabel('add_new');?></a>
    <?php endif; */ ?>
</h2>
