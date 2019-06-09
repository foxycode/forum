<?php declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

final class SignPresenter extends Nette\Application\UI\Presenter
{
    protected function createComponentSignInForm(): Form
    {
        $form = new Form;
        $form->addText('username', 'Přihlašovací jméno:')
            ->setRequired('Zadej uživatelské jméno.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Zadej heslo.');

        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = [$this, 'signInFormSucceeded'];
        return $form;
    }

    public function signInFormSucceeded(Form $form, ArrayHash $values): void
    {
        try {
            $this->getUser()->login($values->username, $values->password);
            $this->getUser()->setExpiration(0, FALSE);
            $this->redirect('Homepage:');

        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

    /**
     * @throws Nette\Application\AbortException
     */
    public function actionOut(): void
    {
        $this->getUser()->logout();
        $this->getSession()->destroy();
        $this->redirect('in');
    }
}
