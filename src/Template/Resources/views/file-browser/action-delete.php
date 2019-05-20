<?php
/**
 * Navigateur de fichier > Formulaire de suppression d'un élement (fichier ou repertoire).
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<div class="Browser-action Browser-action--toggleable Browser-action--delete"
     data-control="file-browser.action.delete"
>
    <h3 class="Browser-title"><?php _e('Supprimer', 'tify'); ?></h3>

    <div class="Browser-actionNotices">
        <?php if ($file->isDir()) : ?>
            <?php echo partial('notice', [
                'type'    => 'warning',
                'content' => __('ATTENTION : Vous vous apprêtez à supprimer un répertoire ainsi que l\'ensemble des ' .
                    'fichiers et dossiers qu\'il contient. Ils ne pourront être récupérés. <br>Êtes vous sûr ?', 'tify')
            ]);
            ?>
        <?php else: ?>
            <?php echo partial('notice', [
                'type'    => 'warning',
                'content' => __('ATTENTION : Vous vous apprêtez à supprimer un fichier. Il ne pourra être récupéré. <br>' .
                    'Êtes vous sûr ?', 'tify')
            ]);
            ?>
        <?php endif; ?>
    </div>
    <form class="Browser-actionForm" method="post" action="" data-control="file-browser.action.delete.form">
        <div class="Browser-actionFormFields">
            <?php echo field('hidden', ['name'  => 'path', 'value' => $file->getRelPath()]); ?>
        </div>

        <div class="Browser-actionFormButtons">
            <?php echo field('button', [
                'attrs'   => [
                    'class' => 'Browser-button Browser-button--valid Browser-actionButton'
                ],
                'type'    => 'submit',
                'content' => __('Valider', 'tify')
            ]); ?>

            <?php echo field('button', [
                'attrs'   => [
                    'class' => 'Browser-button Browser-button--cancel Browser-actionButton',
                    'data-control' => 'file-browser.action.toggle',
                    'data-action'  => 'delete',
                    'data-reset'   => 'true'
                ],
                'type'    => 'button',
                'content' => __('Annuler', 'tify')
            ]); ?>
        </div>
    </form>

    <a href="#"
       class="Browser-actionClose"
       data-control="file-browser.action.toggle"
       data-action="delete">
        <?php echo $this->getIcon('close'); ?>
    </a>
</div>