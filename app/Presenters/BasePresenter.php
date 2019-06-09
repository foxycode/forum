<?php declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
    /**
     * @throws AbortException
     */
    protected function startup(): void
    {
        parent::startup();

        if (!$this->user->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }
}
