<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<?php $this->before(); ?>

    <div <?php $this->attrs(); ?>>
        <div class="tiFyField-ToggleSwitchWrapper">
            <?php
            echo field(
                'radio',
                [
                    'after'   => (string)field(
                        'label',
                        [
                            'content' => $this->get('label_on'),
                            'attrs'   => [
                                'for'   => $this->getId() . '--on',
                                'class' => 'tiFyField-ToggleSwitchLabel tiFyField-ToggleSwitchLabel--on',
                            ],
                        ]
                    ),
                    'attrs'   => [
                        'id'           => $this->getId() . '--on',
                        'class'        => 'tiFyField-ToggleSwitchRadio tiFyField-ToggleSwitchRadio--on',
                        'autocomplete' => 'off',
                    ],
                    'name'    => $this->getName(),
                    'value'   => $this->get('value_on'),
                    'checked' => $this->getValue(),
                ]
            );
            ?>

            <?php
            echo field(
                'radio',
                [
                    'after'   => (string)field(
                        'label',
                        [
                            'content' => $this->get('label_off'),
                            'attrs'   => [
                                'for'   => $this->getId() . '--off',
                                'class' => 'tiFyField-ToggleSwitchLabel tiFyField-ToggleSwitchLabel--off',
                            ],
                        ]
                    ),
                    'attrs'   => [
                        'id'           => $this->getId() . '--off',
                        'class'        => 'tiFyField-ToggleSwitchRadio tiFyField-ToggleSwitchRadio--off',
                        'autocomplete' => 'off',
                    ],
                    'name'    => $this->getName(),
                    'value'   => $this->get('value_off'),
                    'checked' => $this->getValue(),
                ],
                true
            );
            ?>

            <span class="tiFyField-ToggleSwitchHandler"></span>
        </div>
    </div>

<?php $this->after();