<?php
/**
 * Navigateur de fichier > Formulaire de renommage d'un élément.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<div class="Browser-action Browser-action--toggleable Browser-action--newname"
     data-control="file-browser.action.newname"
>
    <div class="Browser-actionNotices"></div>

    <form class="Browser-actionForm" method="post" action="" data-control="file-browser.action.newname.form">
        <div class="Browser-actionFormFields">
            <?php echo field('hidden', ['name'  => 'path', 'value' => $file->getRelPath()]); ?>

            <?php echo field('text', [
                'name'  => 'newname',
                'attrs' => [
                    'placeholder' => __('Saisissez le nouveau nom ...', 'tify')
                ]
            ]); ?>

            <?php if ($file->isFile()) : ?>
                <?php echo field('checkbox', [
                    'after'   => (string)field('label', [
                        'attrs'   => [
                            'for' => 'Browser-actionFormNewname--keep',
                        ],
                        'content' => 'Conserver l\'extension du fichier'
                    ]),
                    'attrs'   => [
                        'id'    => 'Browser-actionFormNewname--keep',
                        'style' => 'display:inline-block'
                    ],
                    'checked' => true,
                    'name'    => 'keep',
                    'value'   => 'on'
                ]); ?>
            <?php else : ?>
                <?php echo field('hidden', [
                    'name'  => 'keep',
                    'value' => 'off'
                ]); ?>
            <?php endif; ?>
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
                    'data-control' => 'file-browser.button.toggle',
                    'data-target'  => "[data-control='file-browser.action.newname']"
                ],
                'type'    => 'button',
                'content' => __('Annuler', 'tify')
            ]); ?>
        </div>
    </form>
</div>