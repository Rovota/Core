<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Kernel\Application;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Localization\LocalizationManager;
use Rovota\Core\Mail\Interfaces\Mailable;
use Rovota\Core\Mail\Traits\Addressing;
use Rovota\Core\Mail\Traits\Advanced;
use Rovota\Core\Mail\Traits\Contents;
use Rovota\Core\Mail\Traits\Testing;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Views\View;
use Throwable;

class Mail implements Mailable
{
	use Addressing, Contents, Advanced, Testing, Conditionable;

	private PHPMailer|null $mailer = null;

	private array|null $always_to = null;

	// -----------------

	public function __construct()
	{
		$this->language = LocalizationManager::getActiveLanguage();

		foreach (MailManager::getDefaults() as $key => $value) {
			if (property_exists($this, $key)) $this->{$key} = $value;
		}

		foreach (MailManager::getConfig() as $key => $value) {
			if (property_exists($this, $key)) $this->{$key} = $value;
		}
	}

	// -----------------

	protected function build(): void
	{
		// Can be used by extending classes.
	}

	public function deliver(): bool
	{
		try {
			foreach ($this->receivers as $receiver) {
				$this->configureMailer();
				$this->build();

				$this->setMailReceiver($receiver);
				$this->setMailData();

				if ($this->mailer->send()) {
					$model = new EmailEntry([
						'email' => $receiver['address'],
						'view' => $this->view === null ?: ($this->view instanceof View ? $this->view::class : $this->view['name']),
						'language_id' => $this->language->id,
						'subject' => $this->subject,
						'type' => $this->type,
					]);
					$model->save();
				}
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::logMessage('warning', 'The email could not be delivered.', [
				'view' => $this->view === null ?: ($this->view instanceof View ? $this->view::class : $this->view['name']), 'throwable' => $throwable,
			]);
			return false;
		}

		return true;
	}

	// -----------------
	// Internal

	/**
	 * @throws \Rovota\Core\Views\Exceptions\MissingViewException
	 * @throws \PHPMailer\PHPMailer\Exception
	 */
	private function setMailData(): void
	{
		// Addressing
		$this->mailer->setFrom($this->from['address'], $this->from['name']);
		$this->mailer->addReplyTo($this->reply_to['address'], $this->reply_to['name']);

		// Contents
		$this->mailer->Subject = $this->subject ?? 'Unknown Subject';
		$this->mailer->Body = $this->render() ?? 'No content provided.';
		$this->mailer->AltBody = $this->text ?? '';

		// Attachments
		foreach ($this->attachments as $attachment) {
			$this->mailer->addStringAttachment($attachment['content'], $attachment['name'], $attachment['encoding'], $attachment['mime_type']);
		}

		// Advanced
		$this->mailer->Priority = $this->priority->value;
		foreach ($this->headers as $name => $value) {
			$this->mailer->addCustomHeader($name, $value);
		}
	}

	/**
	 * @throws \PHPMailer\PHPMailer\Exception
	 */
	private function setMailReceiver(array $receiver): void
	{
		if ($this->always_to !== null) {
			$this->mailer->addAddress($this->always_to['address'], $this->always_to['name']);
			$this->mailer->addCustomHeader('X-To', $receiver['address']);
		} else {
			$this->mailer->addAddress($receiver['address'], $receiver['name']);
		}

		$this->language = $receiver['language'];
		$this->with('mail_receiver_name', $receiver['name']);
		$this->with('mail_receiver_address', $receiver['address']);
	}

	private function configureMailer(): void
	{
		$mailer = new PHPMailer(Application::debugEnabled());
		$mailer->CharSet = PHPMailer::CHARSET_UTF8;
		$mailer->XMailer = Registry::string('mail_program_name', 'Rovota Core');
		$mailer->isHTML();

		if (Registry::bool('mail_smtp_enabled')) {
			$mailer->isSMTP();
			$mailer->Host = Registry::string('mail_smtp_host');
			$mailer->SMTPAuth = true;
			$mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			$mailer->Username = Registry::string('mail_smtp_user');
			$mailer->Password = Registry::string('mail_smtp_password');
			$mailer->Port = Registry::int('mail_smtp_port', 25);
		} else {
			$mailer->isSendmail();
		}

		$this->mailer = $mailer;
	}

}