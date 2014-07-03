<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Sign in/out presenters.
 */
class SignPresenter extends Nette\Application\UI\Presenter
{

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = new Nette\Application\UI\Form;
		$form->addText('username', 'Přihlašovací jméno:')
			->setRequired('Zadej uživatelské jméno.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Zadej heslo.');

		$form->addSubmit('send', 'Přihlásit');

		$form->onSuccess[] = $this->signInFormSucceeded;
		return $form;
	}

	public function signInFormSucceeded($form, $values)
	{
		try {
			$this->getUser()->login($values->username, $values->password);
			$this->getUser()->setExpiration('+1 year', FALSE);
			$this->redirect('Homepage:');

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

	public function actionOut()
	{
		$this->getUser()->logout();
		$this->getSession()->destroy();
		$this->redirect('in');
	}

}
