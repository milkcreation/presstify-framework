<?php
/**
 * Navigateur de fichier > Fichier.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo[] $files
 */
?>
<ul class="Browser-contentFiles" data-control="file-browser.content.items">
    <?php foreach ($files as $file) : ?>
        <li class="Browser-contentFile" data-control="file-browser.content.item">
            <?php $this->insert('file', compact('file')); ?>
        </li>
    <?php endforeach; ?>
</ul>