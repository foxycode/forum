<?php declare(strict_types=1);

namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\Forms\Form as FormRules;
use Nette\Security\User;

final readonly class UserSettingsFormFactory
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

    public function create(User $user): Form
    {
        $form = new Form();

        $form->addSelect('perpage', 'Počet příspěvků', self::PerPageOptions)
            ->addRule(FormRules::Filled, 'Je nutné vybrat počet příspěvků');

        $form->addSelect('sortby', 'Řadit podle', self::SortByOptions)
            ->addRule(FormRules::Filled, 'Je nutné vybrat řazení');

        $form->addSelect('style', 'Vzhled', self::StyleOptions)
            ->addRule(FormRules::Filled, 'Je nutné vybrat vzhled');

        $form->addText('mail', 'E-mail')
            ->setNullable()
            ->addCondition(FormRules::Filled)
            ->addRule(FormRules::Email, 'Musí jít o platný e-mail');

        $form->addText('icq', 'ICQ')
            ->setNullable();

        $form->addText('jabber', 'Jabber')
            ->setNullable();

        $form->addSubmit('send', 'Uložit');

        $form->setDefaults($user->getIdentity()->data);

        return $form;
    }
}
