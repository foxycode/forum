<?php declare(strict_types=1);

namespace App\Presenters;

use App\Model\Repositories\ThreadRepository;

final class HomepagePresenter extends BasePresenter
{
    public function __construct(
        private readonly ThreadRepository $threadRepository,
    ) {
        parent::__construct();
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
