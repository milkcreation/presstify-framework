<?php

namespace tiFy\User\SignIn;

use tiFy\View\ViewController;

/**
 * Class SignInViewController
 *
 * @method SignInItemController formAfter()
 * @method SignInItemController formAdditionnalFields()
 * @method SignInItemController formBefore()
 * @method SignInItemController formBody()
 * @method SignInItemController formErrors()
 * @method SignInItemController formFieldPassword()
 * @method SignInItemController formFieldUsername()
 * @method SignInItemController formFieldRemember()
 * @method SignInItemController formFieldSubmit()
 * @method SignInItemController formFooter()
 * @method SignInItemController formHeader()
 * @method SignInItemController formHiddenFields()
 * @method SignInItemController formInfos()
 * @method SignInItemController lostPasswordLink()
 */
class SignInViewController extends ViewController
{
    /**
     * Affichage de la linéarisation des attributs HTML de la balise form.
     *
     * @return string
     */
    public function attrs()
    {
        echo $this->htmlAttrs($this->get('form.attrs', []));
    }
}