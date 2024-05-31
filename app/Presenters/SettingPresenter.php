<?php declare(strict_types=1);

namespace App\Presenters;

use App\Core\UserManager;
use Nette\Application\UI\Form;
use Nette\Security\SimpleIdentity;
use Nette\Utils\ArrayHash;

final class SettingPresenter extends BasePresenter
{
    public function __construct(
        private readonly UserManager $userManager,
    ) {
        parent::__construct();
    }

    protected function createComponentUserForm(): Form
    {
        $form = new Form();

        $perpage = [
            25 => 25,
            50 => 50,
            75 => 75,
            100 => 100,
            125 => 125,
            150 => 150,
            175 => 175,
            200 => 200,
        ];
        $form->addSelect('perpage', 'Počet příspěvků', $perpage)
            ->addRule(Form::Filled, 'Je nutné vybrat počet příspěvků');

        $sortby = [
            'last_reply_time' => 'Času poslední odpovědi',
            'create_time' => 'Času vytvoření',
        ];
        $form->addSelect('sortby', 'Řadit podle', $sortby)
            ->addRule(Form::Filled, 'Je nutné vybrat řazení');

        $form->addPassword('oldPassword', 'Staré heslo')
            ->setRequired(TRUE)
            ->addRule(function ($item, $arg) {
                return md5($item->value) == $arg;
            }, 'Je nutné zadat platné heslo', $this->getUser()->getIdentity()->data['password']);

        $style = [
            'forum.css' => 'Světle modrý',
            'forum_d.css' => 'Tmavě modrý',
            'forum_b.css' => 'Černý',
        ];
        $form->addSelect('style', 'Vzhled', $style)
            ->addRule(Form::Filled, 'Je nutné vybrat vzhled');

        $form->addText('mail', 'E-mail');
        $form->addText('icq', 'ICQ');
        $form->addText('jabber', 'Jabber');
        $form->addPassword('newPassword1', 'Nové heslo');
        $form->addPassword('newPassword2', 'Kontrola')
            ->setRequired(FALSE)
            ->addConditionOn($form['newPassword1'], Form::Filled)
                ->addRule(Form::EQUAL, 'Hesla se neshodují', $form['newPassword1']);

        $form->addSubmit('send', 'Uložit');

        $form->setDefaults($this->getUser()->getIdentity()->data);

        $form->onSuccess[] = [$this, 'userFormSuccess'];

        return $form;
    }

    public function userFormSuccess(Form $form, ArrayHash $values): void
    {
        unset($values->oldPassword);
        if ($values->newPassword1) {
            $values->password = md5($values->newPassword1);
        }
        unset($values->newPassword1, $values->newPassword2);

        $this->userManager->update($this->user->identity->id, $values);
        $userData = $this->userManager->get($this->getUser()->getIdentity()->getId());

        $this->getUser()->login(new SimpleIdentity($userData->user_id, NULL, $userData->toArray()));

        $form->addError('Údaje změněny');
    }

    public function renderDefault(): void
    {
        $this->template->nick = $this->getUser()->getIdentity()->nick;
    }
}
