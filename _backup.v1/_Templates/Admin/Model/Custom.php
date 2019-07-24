<?php

namespace tiFy\Core\Templates\Admin\Model;

use tiFy\App\Traits\App as TraitsApp;

abstract class Custom
{
    use TraitsApp;

    /**
     * Rendu
     */
    public function render()
    {
?>
<div class="wrap">
    <h2></h2>
</div>
<?php
    }
}