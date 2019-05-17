<?php
/**
 * Navigateur de fichier > Fichier.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<a href="#"
   data-target="<?php echo $file->getRelPath(); ?>"
   data-control="file-browser.link.<?php echo($file->isDir() ? 'dir' : 'file'); ?>"
   class="Browser-contentFileLink Browser-contentFileLink--<?php echo($file->isDir() ? 'dir' : 'file'); ?>"
>
    <div class="Browser-contentFilePreview">
        <?php echo $file->getIcon(); ?>
    </div>
    <div class="Browser-contentFileName">
        <?php echo $file->getBasename(); ?>
    </div>
    <div class="Browser-contentMimetype">
        <?php echo $file->getMimetype(); ?>
    </div>
</a>