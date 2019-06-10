<?php declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
    private const THEMES = [
        'forum.css' => 'green',
        'forum_d.css' => 'blue',
    ];

    /**
     * @throws AbortException
     */
    protected function startup(): void
    {
        parent::startup();

        if (!$this->user->isLoggedIn() && $this->name !== 'Sign') {
            $this->redirect('Sign:in');
        }
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $style = $this->getUser()->getIdentity() ? $this->getUser()->getIdentity()->style : 'green';
        $this->template->theme = self::THEMES[$style] ?? 'green';
    }
}
