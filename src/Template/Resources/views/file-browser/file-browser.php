<?php
/**
 * Navigateur de fichier.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 */
?>
<div class="wrap">
    <div class="Browser" <?php echo $this->htmlAttrs($this->param('attrs', [])); ?>>
        <div class="Browser-sidebar" data-control="file-browser.sidebar">
            <?php $this->insert('sidebar', ['file' => $this->getFile()]); ?>
        </div>

        <div class="Browser-content" data-control="file-browser.content">
            <?php $this->insert('loader'); ?>

            <?php $this->insert('breadcrumb', ['items' => $this->breadcrumb()]); ?>

            <div class="Browser-contentView Browser-contentView--grid">
                <?php if ($files = $this->getFiles()) : ?>
                    <?php $this->insert('files', compact('files')); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>