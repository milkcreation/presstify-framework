<?php
/**
 * Navigateur de fichier > Explorateur de fichiers | Élèment.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<?php if ($file->isDir()) : ?>
    <a href="#"
       class="Browser-explorerItemContent"
       data-path="<?php echo $file->getRelPath(); ?>"
       data-control="file-browser.explorer.browse"
    >
        <?php echo $this->getIcon('expand') . $file->getBasename(); ?>
    </a>
<?php else : ?>
    <span class="Browser-explorerItemContent">
    <?php echo $file->getBasename(); ?>
</span>
<?php endif; ?>