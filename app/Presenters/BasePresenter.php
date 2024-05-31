<?php declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
    private const Themes = [
        'forum.css' => 'green',
        'forum_d.css' => 'blue',
        'forum_b.css' => 'black',
    ];

    /**
     * @throws AbortException
     */
    protected function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn() && $this->getName() !== 'Sign') {
            $this->redirect('Sign:in');
        }
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $style = $this->getUser()->getIdentity() ? $this->getUser()->getIdentity()->style : 'green';
        $this->template->theme = self::Themes[$style] ?? 'green';
    }
}
