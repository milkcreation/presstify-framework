<?php
/**
 * Corps de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Layout\Share\ListTable\ListTableViewController $this.
 */
?>

<tbody id="the-list"<?php echo ($singular = $this->param('singular', '')) ? " data-wp-lists=\"list:{$singular}\"" : ''; ?>>
    <?php if ($items = $this->getItems()) : ?>
        <?php foreach($items as $item) : ?>
            <?php $this->insert('tbody-row', ['item' => $item]); ?>
        <?php endforeach; ?>
    <?php else : ?>
        <tr class="no-items">
            <td class="colspanchange" colspan="<?php echo $this->getColumns()->countVisible(); ?>">
                <?php echo $this->param('no_items'); ?>
            </td>
        </tr>
    <?php endif; ?>
</tbody>
