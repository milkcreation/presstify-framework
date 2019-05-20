<?php
/**
 * Navigateur de fichier > Explorateur de fichiers.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<div class="Browser-explorer">
    <ul class="Browser-explorerItems" data-control="file-browser.explorer.items">
        <li class="Browser-explorerItem Browser-explorerItem--dir" data-control="file-browser.explorer.item">
            <a href="#"
               class="Browser-explorerItemContent"
               data-path="<?php echo $this->getFile('/')->getRelPath(); ?>"
               data-control="file-browser.explorer.browse"
               aria-selected="true"
            >
                <?php echo $this->getIcon('collapse') . __('Racine', 'tify'); ?>
            </a>
            <?php if ($files = $this->getFiles()): ?>
                <?php echo $this->insert('explorer-items', compact('files')); ?>
            <?php endif; ?>
        </li>
    </ul>
</div>
