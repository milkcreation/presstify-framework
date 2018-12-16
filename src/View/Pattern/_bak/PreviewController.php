<?php


add_action(
    "wp_ajax_{$this->getName()}_preview_item",
    [$this, 'wp_ajax_preview_item']
);

/**
 * Récupération ajax de la prévisualisation d'un élément
 *
 * @return string
 */
public function wp_ajax_preview_item()
{
    $this->initParams();

    if (!$item_index = $this->getRequestItemIndex()) :
        die(0);
    endif;

    check_ajax_referer($this->getActionNonce('preview_item', $item_index));

    $this->prepare();
    $item = current($this->items);
    $this->preview_item($item);
    die();
}


/**
 * Récupération de la liste des colonnes de prévisualisation d'un élément
 *
 * @return array
 */
public function get_preview_item_columns()
{
    if (!$preview_item_columns = $this->param('preview_item_columns')) :
        $preview_item_columns = (array)$this->columns();
        unset($preview_item_columns['cb']);
    endif;

    return $preview_item_columns;
}

/**
 * Affichage de l'aperçu des données d'un élément
 *
 * @param object $item Attributs de l'élément courant
 *
 * @return string
 */
public function preview_item($item)
{
    if (!$preview_item_columns = $this->get_preview_item_columns()) :
        return;
    endif;
    ?>
    <table class="form-table">
        <tbody>
        <?php foreach ($preview_item_columns as $column_name => $column_label) :?>
            <tr valign="top">
                <th scope="row">
                    <label><strong><?php echo $column_label;?></strong></label>
                </th>
                <td>
                    <?php echo $this->preview_item_default($item, $column_name); ?>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    <div class="clear"></div>
    <?php
}

/**
 * Affichage de l'aperçu des données d'un élément par défaut
 *
 * @param object $item Attributs de l'élément courant
 * @param string $column_name Identifiant de qualification de la colonne
 *
 * @return string
 */
public function preview_item_default($item, $column_name)
{
    if (method_exists($this, "preview_item_{$column_name}")) :
        return call_user_func([$this, "preview_item_{$column_name}"], $item);
    elseif (method_exists($this, '_column_' . $column_name)) :
        return call_user_func([$this, '_column_' . $column_name], $item);
    elseif (method_exists($this, 'column_' . $column_name)) :
        return call_user_func([$this, 'column_' . $column_name], $item);
    else :
        return $this->getColumnDisplay($column_name, $item);
    endif;
}

/**
 * Aperçu des données des éléments
 *
 * @return string
 */
public function preview_items()
{
    switch($this->param('preview_item_mode')) :
        case 'dialog' :
            ?><div id="Item-previewContainer" class="hidden" style="max-width:800px; min-width:800px;"><div class="Item-previewContent"></div></div><?php
            break;
        case 'row' :
            ?><table class="hidden"><tbody><tr id="Item-previewContainer"><td class="Item-previewContent" colspan="<?php echo $this->columns()->countVisible();?>"><h3><?php _e( 'Chargement en cours ...', 'tify' );?></h3></td></tr></tbody></table><?php
            break;
    endswitch;
}