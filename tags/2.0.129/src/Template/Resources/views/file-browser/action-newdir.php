<?php
/**
 * Navigateur de fichier > Formulaire de création d'un nouveau répertoire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<div class="Browser-action Browser-action--toggleable Browser-action--newdir"
     data-control="file-browser.action.newdir"
>
    <div class="Browser-actionNotices"></div>

    <form class="Browser-actionForm" method="post" action="" data-control="file-browser.action.newdir.form">
        <div class="Browser-actionFormFields">
            <?php echo field('hidden', ['name'  => 'path', 'value' => $file->getRelPath()]); ?>

            <?php echo field('text', [
                'name'  => 'newdir',
                'attrs' => [
                    'placeholder' => __('Saisissez le nom du dossier ...', 'tify')
                ]
            ]); ?>
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
                    'class'        => 'Browser-button Browser-button--cancel Browser-actionButton',
                    'data-control' => 'file-browser.button.toggle',
                    'data-target'  => "[data-control='file-browser.action.newdir']"
                ],
                'type'    => 'button',
                'content' => __('Annuler', 'tify')
            ]); ?>
        </div>
    </form>
</div>