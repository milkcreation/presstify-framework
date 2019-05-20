<?php
/**
 * Navigateur de fichier > Formulaire de création d'un nouveau répertoire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<div class="Browser-action Browser-action--toggleable Browser-action--upload"
     data-control="file-browser.action.upload"
>
    <h3 class="Browser-title"><?php _e('Ajouter des fichiers', 'tify'); ?></h3>

    <div class="Browser-actionNotices"></div>

    <div class="Browser-actionContainer">
        <form action=""
              enctype="multipart/form-data"
              class="Browser-actionForm Browser-actionForm--upload"
              data-control="file-browser.action.upload.form"
        >
            <div class="Browser-actionFormFields">
                <?php echo field('hidden', ['name' => 'action', 'value' => 'upload']); ?>
                <?php echo field('hidden', ['name'  => 'path', 'value' => $file->getRelPath()]); ?>
            </div>
            <div class="Browser-actionFormFallback fallback">
                <input name="file" type="file" multiple />
            </div>
        </form>
        <div class="Browser-actionFormLegend">
            <?php echo $this->getIcon('upload') . __('Cliquez sur la zone ou glisser/déposer des fichiers', 'tify'); ?>
        </div>
    </div>

    <a href="#"
       class="Browser-actionClose"
       data-control="file-browser.action.toggle"
       data-action="upload">
        <?php echo $this->getIcon('close'); ?>
    </a>
</div>