<?php
/**
 * Entête.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\ListTable\Viewer $this
 */
?>
<?php if($page_title = $this->label('page_title')) : ?>
<h2>
    <?php echo $page_title; ?>
    <?php /*if($edit_base_uri = $this->param('edit_base_uri')) : ?>
        <a class="add-new-h2" href="<?php echo $edit_base_uri;?>"><?php echo $this->label('add_new');?></a>
    <?php endif; */ ?>
</h2>
<?php endif; ?>