<?php
/**
 * Navigateur de fichier > Formulaire de renommage d'un élément.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\FileInfo $file
 */
?>
<form method="get" action="" data-control="file-browser.form.newname">
    <?php echo field('hidden', [
        'name'  => 'path',
        'value' => $file->getRelPath()
    ]); ?>
    <?php echo field('text', [
        'name'  => 'newname',
        'attrs' => [
            'placeholder' => __('Saisissez le nouveau nom ...', 'tify')
        ]
    ]); ?>
    <?php echo field('checkbox', [
        'after'   => (string)field('label', [
            'attrs'   => [
                'for' => 'Browser-formRename--keep',
            ],
            'content' => 'Conserver l\'extension du fichier'
        ]),
        'attrs'   => [
            'id'    => 'Browser-formRename--keep',
            'style' => 'display:inline-block'
        ],
        'checked' => true,
        'name'    => 'keep',
        'value'   => 'on'
    ]); ?>
    <br>
    <?php echo field('button', [
        'type'    => 'submit',
        'content' => __('Valider', 'tify')
    ]); ?>
    <?php echo field('button', [
        'type'    => 'button',
        'content' => __('Annuler', 'tify')
    ]); ?>
</form>