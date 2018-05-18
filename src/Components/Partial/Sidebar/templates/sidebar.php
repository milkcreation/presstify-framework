<?php
/**
 * @var string $html_attrs Attributs linéarisé du conteneur.
 * @var string $items Liste des éléments à afficher.
 */
?>

<div <?php echo $html_attrs; ?>>
    <div class="tiFyPartial-SidebarPanel">
        <div class="tiFyPartial-SidebarItemsWrapper">
            <div class="tiFyPartial-SidebarItemsContainer">
                <?php echo $items; ?>
            </div>
        </div>
    </div>
</div>