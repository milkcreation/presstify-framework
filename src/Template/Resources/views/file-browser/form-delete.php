<?php
/**
 * Navigateur de fichier > Formulaire de suppression d'un élement (fichier ou repertoire).
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<?php if ($file->isDir()) : ?>
    <?php echo partial('notice', [
        'type'    => 'warning',
        'content' => __('ATTENTION : Vous vous apprêtez à supprimer un répertoire ainsi que l\'ensemble des fichiers ' .
            'et dossiers qu\'il contient. Ils ne pourront être récupérés. <br>Êtes vous sûr ?', 'tify')
    ]);
    ?>
<?php else: ?>
    <?php echo partial('notice', [
        'type'    => 'warning',
        'content' => __('ATTENTION : Vous vous apprêtez à supprimer un fichier. Ils ne pourra être récupéré. <br>' .
            'Êtes vous sûr ?', 'tify')
    ]);
    ?>
<?php endif; ?>
<form method="get" action="" data-control="file-browser.form.delete">
    <?php echo field('hidden', [
        'name'  => 'path',
        'value' => $file->getRelPath()
    ]); ?>
    <?php echo field('button', [
        'type'    => 'submit',
        'content' => __('Supprimer', 'tify')
    ]); ?>
    <?php echo field('button', [
        'type' => 'button',
        'content' => __('Annuler', 'tify')
    ]); ?>
</form>