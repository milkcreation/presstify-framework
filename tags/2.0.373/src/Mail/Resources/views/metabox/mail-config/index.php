<?php
/**
 * @var tiFy\Contracts\Metabox\MetaboxView $this
 */
?>
<?php if ($info = $this->params('info', true)) : ?>
    <em><?php echo $info; ?></em>
    <hr>
<?php endif; ?>

<?php if ($this->params('enabled.activation', true)) : ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><?php _e('Activation', 'tify'); ?></th>
            <td>
                <?php echo field('toggle-switch', [
                    'name'  => $this->name() . '[enabled]',
                    'value' => filter_var($this->value('enabled'), FILTER_VALIDATE_BOOL) ? 'on' : 'off',
                ]); ?>
            </td>
        </tr>
        </tbody>
    </table>
<?php endif; ?>

<?php if ($this->params('enabled.sender', true)) : ?>
    <h3><?php echo $this->params('sender.title'); ?></h3>

    <?php if ($info = $this->params('sender.info')) : ?>
        <em><?php echo $info; ?></em>
    <?php endif; ?>

    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><?php _e('Email (requis)', 'tify'); ?></th>
            <td>
                <?php echo field('text', [
                    'name'  => $this->name() . '[sender][email]',
                    'value' => $this->value('sender.email'),
                    'attrs' => [
                        'size'         => 40,
                        'autocomplete' => 'off',
                    ],
                ]); ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Nom (optionnel)', 'tify'); ?></th>
            <td>
                <?php echo field('text', [
                    'name'  => $this->name() . '[sender][name]',
                    'value' => $this->value('sender.name'),
                    'attrs' => [
                        'size'         => 40,
                        'autocomplete' => 'off',
                    ],
                ]); ?>
            </td>
        </tr>
        </tbody>
    </table>
<?php endif; ?>

<?php if ($this->params('enabled.recipients', true)) : ?>
    <h3><?php echo $this->params('recipients.title'); ?></h3>

    <?php if ($info = $this->params('recipients.info')) : ?>
        <em><?php echo $info; ?></em>
    <?php endif; ?>

    <?php echo field('repeater', [
        'button' => [
            'content' => __('Ajouter un destinataire', 'tify'),
        ],
        'name'   => $this->name() . '[recipients]',
        'value'  => $this->value('recipients'),
        'viewer' => [
            'override_dir' => dirname($this->path()) . '/repeater',
        ],
    ]); ?>
<?php endif;