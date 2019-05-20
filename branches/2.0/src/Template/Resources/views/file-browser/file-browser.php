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

        <?php $this->insert('explorer'); ?>

        <div class="Browser-content" data-control="file-browser.content" data-view="grid">
            <?php $this->insert('loader'); ?>

            <div class="Browser-contentHeader">
                <?php $this->insert('breadcrumb', ['items' => $this->breadcrumb()]); ?>
                <?php $this->insert('switcher'); ?>
            </div>

            <div class="Browser-contentBody">
                <?php if ($files = $this->getFiles()) : ?>
                    <?php $this->insert('files', compact('files')); ?>
                <?php endif; ?>
            </div>

            <div class="Browser-contentFooter"></div>
        </div>
    </div>
</div>