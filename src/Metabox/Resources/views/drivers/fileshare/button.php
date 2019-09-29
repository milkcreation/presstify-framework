<?php
/**
 * @var tiFy\Contracts\Metabox\MetaboxView $this
 */
?>
<a href="#" data-control="metabox-fileshare.trigger">
    <?php
    echo _n(
        __('Ajouter le fichier', 'tify'),
        __('Ajouter des fichiers', 'tify'),
        (($this->get('max', -1) === 1) ? 1 : 2),
        'tify'
    );
    ?>
</a>
