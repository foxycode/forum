<?php declare(strict_types=1);

namespace App\Presenters;

use App\Core\UserManager;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormRule;
use Nette\Security\SimpleIdentity;
use Nette\Utils\ArrayHash;

final class SettingPresenter extends BasePresenter
{
    private const array PerPageOptions = [
        25 => 25,
        50 => 50,
        75 => 75,
        100 => 100,
        125 => 125,
        150 => 150,
        175 => 175,
        200 => 200,
    ];

    private const array SortByOptions = [
        'last_reply_time' => 'Času poslední odpovědi',
        'create_time' => 'Času vytvoření',
    ];

    private const array StyleOptions = [
        'forum.css' => 'Světle modrý',
        'forum_d.css' => 'Tmavě modrý',
        'forum_b.css' => 'Černý',
    ];

    public function __construct(
        private readonly UserManager $userManager,
    ) {
        parent::__construct();
    }

    protected function createComponentUserForm(): Form
    {
        $form = new Form();

        $form->addSelect('perpage', 'Počet příspěvků', self::PerPageOptions)
            ->addRule(FormRule::Filled, 'Je nutné vybrat počet příspěvků');

        $form->addSelect('sortby', 'Řadit podle', self::SortByOptions)
            ->addRule(FormRule::Filled, 'Je nutné vybrat řazení');

        $form->addPassword('oldPassword', 'Staré heslo')
            ->setRequired('Je nutné zadat staré heslo')
            ->addRule(
                fn ($item, $arg) => md5($item->value) == $arg,
                'Je nutné zadat platné heslo',
                $this->getUser()->getIdentity()->data['password']
            );

        $form->addSelect('style', 'Vzhled', self::StyleOptions)
            ->addRule(FormRule::Filled, 'Je nutné vybrat vzhled');

        $form->addText('mail', 'E-mail')
            ->addCondition(FormRule::Filled)
                ->addRule(FormRule::Email, 'Musí jít o platný e-mail');

        $form->addText('icq', 'ICQ');

        $form->addText('jabber', 'Jabber');

        $form->addPassword('newPassword1', 'Nové heslo');

        $form->addPassword('newPassword2', 'Kontrola')
            ->setRequired(false)
            ->addConditionOn($form['newPassword1'], FormRule::Filled)
                ->addRule(FormRule::Equal, 'Hesla se neshodují', $form['newPassword1']);

        $form->addSubmit('send', 'Uložit');

        $form->setDefaults($this->getUser()->getIdentity()->data);

        $form->onSuccess[] = $this->userFormSuccess(...);

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
