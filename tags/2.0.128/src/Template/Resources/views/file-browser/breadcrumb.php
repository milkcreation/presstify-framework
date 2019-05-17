<?php
/**
 * Navigateur de fichier > Fil d'ariane.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\FileBrowser\Viewer $this
 * @var tiFy\Template\Templates\FileBrowser\Contracts\Breadcrumb|iterable $items
 */
?>
<ol class="Browser-contentBreadcrumb" data-control="file-browser.breadcrumb">
    <li class="Browser-contentBreadcrumbPart BrowserFolder-BreadcrumbPart--root">
        <a href="#"
           class="Browser-contentBreadcrumbPartLink"
           data-control="file-browser.link.dir"
           data-target="/"
        >
            <span class="fa fa-home"></span>
        </a>
    </li>

    <?php foreach($items as $path => $name) : ?>
        <li class="Browser-contentBreadcrumbPart">
            <a href="#"
               class="Browser-contentBreadcrumbPartLink"
               data-control="file-browser.link.dir"
               data-target="<?php echo $path; ?>"
            >
                <?php echo $name; ?>
            </a>
        </li>
    <?php endforeach; ?>
</ol>