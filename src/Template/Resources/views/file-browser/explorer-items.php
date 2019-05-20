<?php
/**
 * Navigateur de fichier > Explorateur de fichiers | Liste des éléments.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileCollection $files
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<ul class="Browser-explorerItems" data-control="file-browser.explorer.items">
    <?php foreach ($files as $file) : ?>
        <li class="Browser-explorerItem Browser-explorerItem--<?php echo $file->isDir() ? 'dir': 'file';?>"
            data-control="file-browser.explorer.item">
            <?php $this->insert('explorer-item', compact('file')); ?>
        </li>
    <?php endforeach; ?>
</ul>