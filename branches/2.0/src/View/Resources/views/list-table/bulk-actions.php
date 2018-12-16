<?php
/**
 * Liste des actions groupés.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\Viewer\Viewer $this
 * @var string $which top|bottom.
 */
?>
<?php if ($this->items()->exists()) : ?>
    <div class="alignleft actions bulkactions">
        <?php echo $this->bulkActions()->which($which); ?>
    </div>
<?php endif;?>
