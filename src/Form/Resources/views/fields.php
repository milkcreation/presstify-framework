<?php
/**
 * Liste des champs du formulaire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FactoryView $this
 * @var tiFy\Contracts\Form\FactoryFields|tiFy\Contracts\Form\FactoryField[] $fields
 */
?>
<?php if ($fields) : ?>
    <div class="Form-fields">
        <?php if ($fields->hasGroup()) : ?>
            <?php foreach ($fields->byGroup() as $num => $groupFields) : ?>
                <div class="Form-fieldsGroup Form-fieldsGroup--<?php echo $num; ?>">
                    <?php foreach ($groupFields as $field) : ?>
                        <?php $this->insert('field', compact('field')); ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <?php foreach ($fields as $field) : ?>
                <?php $this->insert('field', compact('field')); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>