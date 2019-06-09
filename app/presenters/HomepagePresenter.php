<?php declare(strict_types=1);

namespace App\Presenters;

use App\Model\ThreadRepository;

final class HomepagePresenter extends BasePresenter
{
    /**
     * @var ThreadRepository
     */
    private $threadRepository;

    public function __construct(ThreadRepository $threadRepository)
    {
        parent::__construct();
        $this->threadRepository = $threadRepository;
    }

    public function actionDefault(): void
    {
    }

    public function actionSearch(): void
    {
    }

    public function renderDefault(): void
    {
        $this->template->perpage = $this->getUser()->getIdentity()->perpage;
        $this->template->threads = $this->threadRepository->getLast(
            $this->getUser()->getIdentity()->id,
            $this->getUser()->getIdentity()->sortby,
            $this->getUser()->getIdentity()->perpage
        );
    }

    public function renderSearch(?string $q): void
    {
        $this->template->q = $q;
        $this->template->threads = $this->threadRepository->search(
            $q,
            $this->getUser()->getIdentity()->sortby,
            $this->getUser()->getIdentity()->perpage
        );
    }
}
