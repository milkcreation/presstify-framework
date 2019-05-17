<?php
/**
 * Navigateur de fichier > Indicateur de chargement.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 */
?>
<div class="Browser-contentLoader">
    <?php echo partial('spinner', [
        'type' => 'spinner-pulse'
    ]); ?>
</div>
