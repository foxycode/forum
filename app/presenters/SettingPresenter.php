<?php

namespace App\Presenters;

use Nette,
	Nette\Application\UI\Form,
	Nette\Application\BadRequestException;


/**
 * Setting presenter.
 */
class SettingPresenter extends BasePresenter
{
	/** @var \App\Model\UserManager @inject */
	public $userManager;

	// -------------------------------------------------------------------------

	protected function createComponentUserForm()
	{
		$form = new Form();

		$perpage = array(
			25 => 25,
			50 => 50,
			75 => 75,
			100 => 100,
			125 => 125,
			150 => 150,
			175 => 175,
			200 => 200
		);
		$form->addSelect('perpage', 'Počet příspěvků', $perpage)
			->addRule(Form::FILLED, 'Je nutné vybrat počet příspěvků');

		$sortby = array(
			'last_reply_time' => 'Času poslední odpovědi',
			'create_time' => 'Času vytvoření'
		);
		$form->addSelect('sortby', 'Řadit podle', $sortby)
			->addRule(Form::FILLED, 'Je nutné vybrat řazení');

		$form->addPassword('oldPassword', 'Staré heslo')
			->addRule(function ($item, $arg) {
				return md5($item->value) == $arg;
			}, 'Je nutné zadat platné heslo', $this->user->identity->data['password']);

		$style = array(
			'forum.css' => 'Světle modrý',
			'forum_d.css' => 'Tmavě modrý'
		);
		$form->addSelect('style', 'Vzhled', $style)
			->addRule(Form::FILLED, 'Je nutné vybrat vzhled');

		$form->addText('mail', 'E-mail');
		$form->addText('icq', 'ICQ');
		$form->addText('jabber', 'Jabber');
		$form->addPassword('newPassword1', 'Nové heslo');
		$form->addPassword('newPassword2', 'Kontrola')
			->addConditionOn($form['newPassword1'], Form::FILLED)
				->addRule(Form::EQUAL, 'Hesla se neshodují', $form['newPassword1']);

		$form->addSubmit('send', 'Uložit');

		$form->setDefaults($this->user->identity->data);

		$form->onSuccess[] = array($this, 'userFormSuccess');
		return $form;
	}

	public function userFormSuccess(Form $form)
	{
		$values = $form->getValues();

		unset($values->oldPassword);
		if ($values->newPassword1)
		{
			$values->password = $values->newPassword1;
		}
		unset($values->newPassword1, $values->newPassword2);

		$this->userManager->update($this->user->identity->id, $values);
		$userData = $this->userManager->get($this->user->identity->id);

		$this->user->login(
			new Nette\Security\Identity($userData->user_id, NULL, $userData->toArray())
		);

		$form->addError('Údaje změněny');
	}

	// -------------------------------------------------------------------------

	public function renderDefault($id)
	{
		$this->template->nick = $this->user->identity->data['nick'];
	}

}
