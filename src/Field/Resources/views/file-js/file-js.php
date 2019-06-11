<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>
<?php $this->before(); ?>
<div <?php echo $this->htmlAttrs($this->get('attrs', [])); ?>>
    <form action=""
          enctype="multipart/form-data"
          class="FileManager-actionForm FileManager-actionForm--upload"
          data-control="file-manager.action.upload.form"
    >
        <div class="FileManager-actionFormFields">
            <?php echo field('hidden', ['name' => 'action', 'value' => 'upload']); ?>
            <?php echo field('hidden', ['name'  => 'path', 'value' => $file->getRelPath()]); ?>
        </div>
        <div class="FileManager-actionFormFallback fallback">
            <input name="file" type="file" multiple />
        </div>
    </form>
    <div class="FileManager-actionFormLegend">
        <?php echo $this->getIcon('upload') . __('Cliquez sur la zone ou glisser/déposer des fichiers', 'tify'); ?>
    </div>
</div>
<?php $this->after();