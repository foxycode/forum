<?php

namespace App\Presenters;

use App\Model\ThreadRepository;

final class HomepagePresenter extends BasePresenter
{
    /**
     * @var ThreadRepository
     */
    private $threadRepository;

    /**
     * @var int
     */
    private $perpage;

    public function __construct(ThreadRepository $threadRepository)
    {
        parent::__construct();
        $this->threadRepository = $threadRepository;
    }

    public function actionDefault(): void
    {
        $this->perpage = $this->user->identity->data['perpage'];
    }

    public function actionSearch(): void
    {
    }

    public function renderDefault(): void
    {
        $this->template->perpage = $this->perpage;
        $this->template->threads = $this->threadRepository->getLast(
            $this->getUser()->getIdentity()->id,
            $this->getUser()->getIdentity()->data['sortby'],
            $this->getUser()->getIdentity()->data['perpage']
        );
    }

    public function renderSearch(): void
    {
        $this->template->q = $this->getParameter('q');
        $this->template->threads = $this->threadRepository->search(
            $this->getParameter('q'),
            $this->getUser()->getIdentity()->data['sortby'],
            $this->getUser()->getIdentity()->data['perpage']
        );
    }
}
