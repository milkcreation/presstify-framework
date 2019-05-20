<?php
/**
 * Navigateur de fichier > Fichier.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<a href="#"
   data-path="<?php echo $file->getRelPath(); ?>"
   data-control="file-browser.action.get"
   class="Browser-contentFileLink Browser-contentFileLink--<?php echo($file->isDir() ? 'dir' : 'file'); ?>"
>
    <div class="Browser-contentFileAttr Browser-contentFileAttr--preview">
        <?php echo $file->getIcon(); ?>
    </div>

    <div class="Browser-contentFileAttr Browser-contentFileAttr--name">
        <?php echo $file->getBasename(); ?>
    </div>

    <div class="Browser-contentFileAttr Browser-contentFileAttr--size">
        <?php echo $file->getHumanSize(); ?>
    </div>

    <div class="Browser-contentFileAttr Browser-contentFileAttr--date">
        <?php echo $file->getHumanDate('d/m/Y H:i'); ?>
    </div>
</a>