<?php

namespace App\Presenters;

use Nette,
	Nette\Application\UI\Form,
	Nette\Application\BadRequestException;


/**
 * Thread presenter.
 */
class ThreadPresenter extends BasePresenter
{
	/** @var \App\Model\ThreadRepository @inject */
	public $threadRepository;

	/** @var \App\Model\MessageRepository @inject */
	public $messageRepository;

	/** @var Nette\Database\Row */
	private $thread;

	// -------------------------------------------------------------------------

	protected function createComponentMessageForm()
	{
		$form = new Form();

		if ($this->getParameter('action') == 'new')
		{
			$form->addText('subject', 'Předmět')
				->addRule(Form::MAX_LENGTH, 'Maximální povolená délka předmětu je %d znaků.', 42)
				->addRule(Form::FILLED, 'Je nutné vyplnit předmět.');
		}

		$form->addTextarea('text', 'Zpráva')
			->addRule(Form::FILLED, 'Je nutné vyplnit zprávu.');

		$form->addSubmit('preview', 'Náhled');
		$form->addSubmit('submit', 'Odeslat zprávu');

		$form->onSuccess[] = array($this, 'messageFormSuccess');
		$form->onError[] = array($this,'messageFormError');
		return $form;
	}

	public function messageFormSuccess(Form $form)
	{
		if ($this->getParameter('id') && !$this->thread)
		{
			throw new BadRequestException('Příspěvek nebyl nalezen.');
		}

		$values = $form->getValues();
		$values->text = $this->processMessageBody($values->text);

		if ($form->isSubmitted()->name == 'preview')
		{
			$values->nick = $this->getUser()->getIdentity()->data['nick'];
			$values->mail = $this->getUser()->getIdentity()->data['mail'];
			$values->icq = $this->getUser()->getIdentity()->data['icq'];
			$values->jabber = $this->getUser()->getIdentity()->data['jabber'];
			$values->create_time = new \DateTime;
			$this->template->preview = $values;
		}
		else
		{
			$this->messageRepository->begin();

			$values->submiter_id = $this->getUser()->getIdentity()->id;
			$values->create_time = new \DateTime;

			if ($this->getParameter('id'))
			{
				$values->thread_id = $thread_id = $this->getParameter('id');
				$replies = $this->threadRepository->addMessage($values);
			}
			else
			{
				$thread_id = $this->threadRepository->add($values);
				$replies = 0;
			}

			$this->messageRepository->commit();

			$this->redirect('default#'.$replies, $thread_id);
		}
	}

	public function messageFormError($form)
	{
		foreach ($form->getErrors() as $error)
		{
			$this->flashMessage($error);
		}
	}

	// -------------------------------------------------------------------------

	private function processMessageBody($body)
	{
		$body = htmlspecialchars(stripslashes($body));
		$body = str_replace("&amp;", "&", $body);
		$body = str_replace("&lt;b&gt;", "<b>", $body);
		$body = str_replace("&lt;i&gt;", "<i>", $body);
		$body = str_replace("&lt;u&gt;", "<u>", $body);
		$body = str_replace("&lt;/b&gt;", "</b>", $body);
		$body = str_replace("&lt;/i&gt;", "</i>", $body);
		$body = str_replace("&lt;/u&gt;", "</u>", $body);
		$body = str_replace("&lt;B&gt;", "<B>", $body);
		$body = str_replace("&lt;I&gt;", "<I>", $body);
		$body = str_replace("&lt;U&gt;", "<U>", $body);
		$body = str_replace("&lt;/B&gt;", "</B>", $body);
		$body = str_replace("&lt;/I&gt;", "</I>", $body);
		$body = str_replace("&lt;/U&gt;", "</U>", $body);

		$body = preg_replace("~http://www([^ \n]+)~i", "<A HREF=\"http://www\\1\" TARGET=\"_blank\">hhtp://www\\1</A>", $body);
		$body = preg_replace("~http://([^ \n]*)~i", "<A HREF=\"http://\\1\" TARGET=\"_blank\">http://\\1</A>", $body);
		$body = preg_replace("~https://www([^ \n]+)~i", "<A HREF=\"https://www\\1\" TARGET=\"_blank\">hhtps://www\\1</A>", $body);
		$body = preg_replace("~https://([^ \n]*)~i", "<A HREF=\"https://\\1\" TARGET=\"_blank\">https://\\1</A>", $body);
		$body = preg_replace("~www\.([^ \n]*)~i", "<A HREF=\"http://www.\\1\" TARGET=\"_blank\">www.\\1</A>", $body);

		$body = preg_replace("~ftp://ftp([^ \n]*)~i", "<A HREF=\"ftp://ftp\\1\" TARGET=\"_blank\">ftp://ftp\\1</A>", $body);
		$body = preg_replace("~ftp://([^ \n]*)~i", "<A HREF=\"ftp://\\1\" TARGET=\"_blank\">ftp://\\1</A>", $body);
		$body = preg_replace("~ftp\.([^ \n]*)~i", "<A HREF=\"ftp://ftp.\\1\" TARGET=\"_blank\">ftp.\\1</A>", $body);

		$body = preg_replace("~mailto:([^ \n]*)~i", "<A HREF=\"mailto:\\1\">\\1</A>", $body);

		for($z=1;$z<=1765;$z++) 
		{
			$body = str_replace("*$z*", "<img src=\"http://img.xchat.centrum.cz/images/x4/sm/"
									. (substr($z,strlen($z)-2,2)+0) . "/$z.gif\" alt=\"*$z*\" />", $body);
		}

		return $body;
	}

	// -------------------------------------------------------------------------

	public function actionDefault($id)
	{
		if (!$id)
		{
			throw new BadRequestException('Příspěvek nebyl nalezen.');
		}

		$this->thread = $this->threadRepository->get(
			$id, $this->getUser()->getIdentity()->id
		);

		$this->threadRepository->updateRead($this->thread, $this->getUser()->getIdentity()->id);

		if (!$this->thread)
		{
			throw new BadRequestException('Příspěvek nebyl nalezen.');
		}
	}

	public function actionReply($id)
	{
		$this->thread = $this->threadRepository->get(
			$id, $this->getUser()->getIdentity()->id
		);

		if (!$this->thread)
		{
			throw new BadRequestException('Příspěvek nebyl nalezen.');
		}
	}

	public function actionNew()
	{
	}

	// -------------------------------------------------------------------------

	public function renderDefault($id)
	{
		$this->template->thread = $this->thread;
		$this->template->messages = $this->messageRepository->getByThreadId($id);
		$this->template->preview = $this->getParameter('preview') == 'yes' ? TRUE : FALSE;
	}

	public function renderReply($id)
	{
		$this->template->thread = $this->thread;
	}

	public function renderNew()
	{
		$this->template->showSubject = TRUE;
	}

}
