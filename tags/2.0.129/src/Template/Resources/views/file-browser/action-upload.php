<?php
/**
 * Navigateur de fichier > Formulaire de création d'un nouveau répertoire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<div class="Browser-action Browser-action--newdir"
     data-control="file-browser.action.upload"
>
    <div class="Browser-actionNotices"></div>

    <form action=""
          enctype="multipart/form-data"
          class="Browser-actionForm Browser-actionForm--upload"
          data-control="file-browser.action.upload.form"
    >
        <div class="Browser-actionFormFields">
            <?php echo field('hidden', ['name' => 'action', 'value' => 'upload']); ?>
            <?php echo field('hidden', ['name'  => 'path', 'value' => $file->getRelPath()]); ?>
        </div>
        <div class="fallback">
            <input name="file" type="file" multiple />
        </div>
    </form>
</div>