<?php
/**
 * Navigateur de fichier > Barre latérale.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<div class="Browser-sidebarInfos" data-control="file-browser.sidebar.file-infos">
    <?php $this->insert('file-infos', compact('file')); ?>
</div>
<hr>
<div class="Browser-sidebarAction">
    <div class="Browser-sidebarAction--newdir">
        <?php $this->insert('form-newdir', compact('file')); ?>
        <a href="#" class="Browser-button"><?php _e('Créer un dossier', 'tify'); ?></a>
    </div>
</div>
