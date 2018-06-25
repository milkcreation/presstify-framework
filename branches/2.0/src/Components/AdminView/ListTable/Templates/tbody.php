<?php
/**
 * Corps de la table.
 *
 * @var tiFy\Components\AdminView\ListTable\TemplateController $this
 */
?>

<tbody id="the-list"<?php echo ($singular = $this->params()->get('singular', '')) ? " data-wp-lists=\"list:{$singular}\"" : ''; ?>>
    <?php if ($items = $this->getItems()) : ?>
        <?php foreach($items as $item) : ?>
            <?php $this->partial('tbody-row', ['item' => $item]); ?>
        <?php endforeach; ?>
    <?php else : ?>
        <tr class="no-items">
            <td class="colspanchange" colspan="<?php echo $this->getColumns()->countVisible(); ?>">
                <?php echo $this->params()->get('no_items'); ?>
            </td>
        </tr>
    <?php endif; ?>
</tbody>
