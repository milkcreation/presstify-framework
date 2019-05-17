<?php
/**
 * Navigateur de fichier > Formulaire de création d'un nouveau répertoire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<form method="get" action="" data-control="file-browser.form.newdir">
    <?php echo field('hidden', [
        'name' => 'path',
        'value' => $file->getRelPath()
    ]); ?>
    <?php echo field('text', [
        'name' => 'newdir',
        'attrs' => [
            'placeholder' => __('Saisissez le nom du dossier ...', 'tify')
        ]
    ]); ?>
    <?php echo field('button', [
        'type' => 'submit',
        'content' => __('Valider', 'tify')
    ]); ?>
    <?php echo field('button', [
        'type' => 'button',
        'content' => __('Annuler', 'tify')
    ]); ?>
</form>