<?php
/**
 * Navigateur de fichier > Formulaire de renommage d'un élément.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<div class="Browser-action Browser-action--toggleable Browser-action--rename"
     data-control="file-browser.action.rename"
>
    <h3 class="Browser-title"><?php _e('Renommer', 'tify'); ?></h3>

    <div class="Browser-actionNotices"></div>

    <form class="Browser-actionForm" method="post" action="" data-control="file-browser.action.rename.form">
        <div class="Browser-actionFormFields">
            <?php echo field('hidden', ['name' => 'path', 'value' => $file->getRelPath()]); ?>

            <div class="Browser-actionFormField Browser-actionFormField--name">
                <?php echo field('text', [
                    'name'  => 'name',
                    'attrs' => [
                        'placeholder' => __('Saisissez le nouveau nom ...', 'tify')
                    ]
                ]); ?>
                <?php if ($file->isFile()) : ?>
                    <div class="Browser-actionFormExt">
                        <?php echo ".{$file->getExtension()}"; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($file->isFile()) : ?>
                <?php echo field('checkbox', [
                    'after'   => (string)field('label', [
                        'attrs'   => [
                            'for' => 'Browser-actionFormRename--keep',
                        ],
                        'content' => 'Conserver l\'extension du fichier'
                    ]),
                    'attrs'   => [
                        'id'          => 'Browser-actionFormRename--keep',
                        'style'       => 'display:inline-block',
                        'data-toggle' => '.Browser-actionFormExt'
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
                    'class'        => 'Browser-button Browser-button--cancel Browser-actionButton',
                    'data-control' => 'file-browser.action.toggle',
                    'data-action'  => 'rename',
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
       data-action="rename">
        <?php echo $this->getIcon('close'); ?>
    </a>
</div>