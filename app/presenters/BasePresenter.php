<?php declare(strict_types=1);

namespace App\Presenters;

use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /**
     * @throws Nette\Application\AbortException
     */
    protected function startup(): void
    {
        parent::startup();

        if (!$this->user->isLoggedIn())
        {
            $this->redirect('Sign:in');
        }
    }
}
