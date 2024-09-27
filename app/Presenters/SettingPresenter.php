<?php declare(strict_types=1);

namespace App\Presenters;

use App\Core\UserManager;
use App\Forms\UserPasswordFormFactory;
use App\Forms\UserSettingsFormFactory;
use Nette\Application\UI\Form;
use Nette\Security\SimpleIdentity;
use Nette\Utils\ArrayHash;

/**
 * @property-read SettingsTemplate $template
 */
final class SettingPresenter extends BasePresenter
{
    public function __construct(
        private readonly UserPasswordFormFactory $userPasswordFormFactory,
        private readonly UserSettingsFormFactory $userSettingsFormFactory,
        private readonly UserManager $userManager,
    ) {
        parent::__construct();
    }

    public function renderDefault(): void
    {
        $this->template->nick = $this->getUser()->getIdentity()->nick;
    }

    protected function createComponentPasswordForm(): Form
    {
        $form = $this->userPasswordFormFactory->create($this->getUser());
        $form->onSuccess[] = $this->passwordFormSuccess(...);

        return $form;
    }

    protected function createComponentSettingsForm(): Form
    {
        $form = $this->userSettingsFormFactory->create($this->getUser());
        $form->onSuccess[] = $this->settingsFormSuccess(...);

        return $form;
    }

    private function passwordFormSuccess(Form $form, ArrayHash $values): void
    {
        $this->userManager->update($this->getUser()->getId(), [
            'password' => md5($values->newPassword1),
        ]);

        $userData = $this->userManager->get($this->getUser()->getId());
        $this->getUser()->login(new SimpleIdentity($userData->user_id, null, $userData->toArray()));

        $form->addError('Heslo změněno');
    }

    private function settingsFormSuccess(Form $form, ArrayHash $values): void
    {
        $this->userManager->update($this->getUser()->getId(), (array) $values);

        $userData = $this->userManager->get($this->getUser()->getId());
        $this->getUser()->login(new SimpleIdentity($userData->user_id, null, $userData->toArray()));

        $form->addError('Nastavení změněna');
    }
}
