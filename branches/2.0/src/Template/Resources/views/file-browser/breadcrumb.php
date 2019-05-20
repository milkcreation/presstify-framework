<?php
/**
 * Navigateur de fichier > Fil d'ariane.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\Breadcrumb|iterable $items
 */
?>
<ol class="Browser-breadcrumb" data-control="file-browser.breadcrumb">
    <li class="Browser-breadcrumbPart">
        <a href="#"
           class="Browser-breadcrumbPartLink"
           data-control="file-browser.action.get"
           data-path="/"
        >
            <?php echo $this->getIcon('home'); ?>
        </a>
    </li>

    <?php foreach($items as $path => $name) : ?>
        <li class="Browser-breadcrumbPart">
            <a href="#"
               class="Browser-breadcrumbPartLink"
               data-control="file-browser.action.get"
               data-path="<?php echo $path; ?>"
            >
                <?php echo $name; ?>
            </a>
        </li>
    <?php endforeach; ?>
</ol>