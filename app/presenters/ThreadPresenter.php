<?php declare(strict_types=1);

namespace App\Presenters;

use App\Model\MessageRepository;
use App\Model\ThreadRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Row;
use Nette\Utils\ArrayHash;

final class ThreadPresenter extends BasePresenter
{
    /**
     * @var MessageRepository
     */
    private $messageRepository;

    /**
     * @var ThreadRepository
     */
    private $threadRepository;

    /**
     * @var Row
     */
    private $thread;

    public function __construct(MessageRepository $messageRepository, ThreadRepository $threadRepository)
    {
        parent::__construct();
        $this->messageRepository = $messageRepository;
        $this->threadRepository = $threadRepository;
    }

    protected function createComponentMessageForm(): Form
    {
        $form = new Form();

        if ($this->getParameter('action') === 'new') {
            $form->addText('subject', 'Předmět')
                ->addRule(Form::MAX_LENGTH, 'Maximální povolená délka předmětu je %d znaků.', 42)
                ->addRule(Form::FILLED, 'Je nutné vyplnit předmět.');
        }

        $form->addTextarea('text', 'Zpráva')
            ->addRule(Form::FILLED, 'Je nutné vyplnit zprávu.');

        $form->addSubmit('preview', 'Náhled');
        $form->addSubmit('submit', 'Odeslat zprávu');

        $form->onSuccess[] = [$this, 'messageFormSuccess'];
        $form->onError[] = [$this, 'messageFormError'];
        return $form;
    }

    /**
     * @throws BadRequestException
     * @throws AbortException
     */
    public function messageFormSuccess(Form $form, ArrayHash $values): void
    {
        if ($this->getParameter('id') && !$this->thread) {
            throw new BadRequestException('Příspěvek nebyl nalezen.');
        }

        $values->text = $this->processMessageBody($values->text);

        if ($form->isSubmitted()->name === 'preview') {
            $values->nick = $this->getUser()->getIdentity()->data['nick'];
            $values->mail = $this->getUser()->getIdentity()->data['mail'];
            $values->icq = $this->getUser()->getIdentity()->data['icq'];
            $values->jabber = $this->getUser()->getIdentity()->data['jabber'];
            $values->create_time = new \DateTime;
            $this->template->preview = $values;
        } else {
            $this->messageRepository->begin();

            $values->submiter_id = $this->getUser()->getIdentity()->id;
            $values->create_time = new \DateTime;

            if ($this->getParameter('id')) {
                $values->thread_id = $thread_id = $this->getParameter('id');
                $replies = $this->threadRepository->addMessage($values);
            } else {
                $thread_id = $this->threadRepository->add($values);
                $replies = 0;
            }

            $this->messageRepository->commit();

            $this->redirect('default#' . $replies, $thread_id);
        }
    }

    public function messageFormError(Form $form): void
    {
        foreach ($form->getErrors() as $error) {
            $this->flashMessage($error);
        }
    }

    private function processMessageBody(string $body): string
    {
        $body = htmlspecialchars(stripslashes($body));
        $body = str_replace([
            '&amp;',
            '&lt;b&gt;',
            '&lt;i&gt;',
            '&lt;u&gt;',
            '&lt;/b&gt;',
            '&lt;/i&gt;',
            '&lt;/u&gt;',
            '&lt;B&gt;',
            '&lt;I&gt;',
            '&lt;U&gt;',
            '&lt;/B&gt;',
            '&lt;/I&gt;',
            '&lt;/U&gt;',
            'www.',
            '//http://',
        ], [
            '&',
            '<b>',
            '<i>',
            '<u>',
            '</b>',
            '</i>',
            '</u>',
            '<b>',
            '<i>',
            '<u>',
            '</b>',
            '</i>',
            '</u>',
            'http://www.',
            '//',
        ], $body);

        $body = preg_replace(
            "~http(s?)://([^ \n]*)~i",
            '<a href="http\\1://\\2" target="_blank">http\\1://\\2</a>',
            $body
        );

        $body = str_replace(['ftp.', '//ftp://'], ['ftp://ftp.', '//'], $body);
        $body = preg_replace("~ftp://([^ \n]*)~i", '<a href="ftp://\\1" target="_blank">ftp://\\1</a>', $body);

        $body = preg_replace("~mailto:([^ \n]*)~i", '<a href="mailto:\\1">\\1</a>', $body);

        for ($z = 1; $z <= 1765; $z++) {
            $body = str_replace("*$z*", '<img src="http://img.xchat.centrum.cz/images/x4/sm/'
                . (substr((string) $z, strlen((string) $z) - 2, 2) + 0) . "/$z.gif\" alt=\"*$z*\" />", $body);
        }

        return $body;
    }

    /**
     * @throws BadRequestException
     */
    public function actionDefault(int $id): void
    {
        $this->thread = $this->threadRepository->get($id, $this->getUser()->getIdentity()->getId());
        if (!$this->thread) {
            throw new BadRequestException('Příspěvek nebyl nalezen.');
        }

        $this->threadRepository->updateRead($this->thread, $this->getUser()->getIdentity()->id);

        if (!$this->thread) {
            throw new BadRequestException('Příspěvek nebyl nalezen.');
        }
    }

    /**
     * @throws BadRequestException
     */
    public function actionReply(int $id): void
    {
        $this->thread = $this->threadRepository->get($id, $this->getUser()->getIdentity()->getId());

        if (!$this->thread) {
            throw new BadRequestException('Příspěvek nebyl nalezen.');
        }
    }

    public function actionNew(): void
    {
    }

    public function renderDefault(int $id): void
    {
        $this->template->thread = $this->thread;
        $this->template->messages = $this->messageRepository->getByThreadId($id);
        $this->template->preview = $this->getParameter('preview') === 'yes' ? TRUE : FALSE;
    }

    public function renderReply(): void
    {
        $this->template->thread = $this->thread;
    }

    public function renderNew(): void
    {
        $this->template->showSubject = TRUE;
    }
}
