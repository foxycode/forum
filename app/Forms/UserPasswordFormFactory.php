<?php declare(strict_types=1);

namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\Forms\Form as FormRules;
use Nette\Security\User;

final readonly class UserPasswordFormFactory
{
    public function create(User $user): Form
    {
        $form = new Form();

        $form->addPassword('oldPassword', 'Staré heslo')
            ->setRequired('Je nutné zadat staré heslo')
            ->addRule(
                fn ($item, $arg) => md5($item->value) == $arg,
                'Je nutné zadat platné heslo',
                $user->getIdentity()->data['password']
            );

        $form->addPassword('newPassword1', 'Nové heslo');

        $form->addPassword('newPassword2', 'Kontrola')
            ->setRequired(false)
            ->addConditionOn($form['newPassword1'], FormRules::Filled)
            ->addRule(FormRules::Equal, 'Hesla se neshodují', $form['newPassword1']);

        $form->addSubmit('send', 'Uložit');

        return $form;
    }
}
