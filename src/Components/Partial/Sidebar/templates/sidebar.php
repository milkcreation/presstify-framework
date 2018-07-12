<?php
/**
 * @var tiFy\Partial\TemplateController $this
 * @var string $html_attrs Attributs linéarisé du conteneur.
 * @var string $items Liste des éléments à afficher.
 */
?>

<?php $this->before(); ?>

<div <?php echo $this->attrs(); ?>>

    <?php echo $this->toggle(); ?>

    <div class="tiFyPartial-SidebarPanel">
        <div class="tiFyPartial-SidebarItemsHeader">
            <?php echo $this->header(); ?>
        </div>

        <div class="tiFyPartial-SidebarItemsBody">
            <?php if ($items = $this->get('items', [])) : ?>
                <ul class="tiFyPartial-SidebarItems">
                <?php foreach($items as $item) : ?>
                    <li <?php echo $this->htmlAttrs($item->get('attrs')); ?>><?php echo $item; ?></li>
                <?php endforeach;?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="tiFyPartial-SidebarItemsFooter">
            <?php echo $this->footer(); ?>
        </div>
    </div>
</div>

<?php $this->after(); ?>