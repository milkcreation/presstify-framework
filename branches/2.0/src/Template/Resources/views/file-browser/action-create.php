<?php
/**
 * Navigateur de fichier > Formulaire de création d'un nouveau répertoire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<div class="Browser-action Browser-action--toggleable Browser-action--create"
     data-control="file-browser.action.create"
>
    <h3 class="Browser-title"><?php _e('Créer un dossier', 'tify'); ?></h3>

    <div class="Browser-actionNotices"></div>

    <form class="Browser-actionForm" method="post" action="" data-control="file-browser.action.create.form">
        <div class="Browser-actionFormFields">
            <?php echo field('hidden', ['name'  => 'path', 'value' => $file->getRelPath()]); ?>

            <?php echo field('text', [
                'name'  => 'name',
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
                    'data-control' => 'file-browser.action.toggle',
                    'data-action'  => 'create',
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
       data-action="create">
        <?php echo $this->getIcon('close'); ?>
    </a>
</div>