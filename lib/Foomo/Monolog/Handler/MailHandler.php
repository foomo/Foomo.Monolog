<?php

namespace Foomo\Monolog\Handler;

use Foomo\Monolog\Module;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Logger;

/**
 * MailHandler uses Foomo's SMTP functions to send mails
 *
 * @author Boštjan Marušič <bostjan.marusic@bestbytes.com>
 * @author Frederik Löffert <frederik.loeffert@bestbytes.com>
 */
class MailHandler extends NativeMailerHandler
{
	private $from;

	private $replyTo;

	public function __construct($to, $subject, $from, $replyTo, $level = Logger::ERROR, $bubble = true, $maxColumnWidth = 70)
	{
		parent::__construct($to, $subject, $from, $level, $bubble);
		$this->from = $from;
		$this->replyTo = $replyTo;
		$this->to = is_array($to) ? $to : array($to);
		$this->subject = $subject;
		$this->addHeader(sprintf('From: %s', $from));
		$this->maxColumnWidth = $maxColumnWidth;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function send($content, array $records)
	{
		$smtpConfig = \Foomo\Config::getConf(Module::NAME, \Foomo\Config\Smtp::NAME);
		if ($smtpConfig) {
			$content = wordwrap($content, $this->maxColumnWidth);
			$headers = [];
			$headers['from'] = $this->from;
			$headers['reply-to'] = $this->replyTo;

			$mailer = new \Foomo\Mailer();
			$mailer->setSmtpConfig($smtpConfig);
			foreach ($this->to as $to) {

				$success = $mailer->sendMail(
					$to,
					$this->subject,
					$content,
					'',
					$headers
				);
				if (!$success) {
					trigger_error('could not send contact mail ' . $mailer->getLastError(), \E_USER_WARNING);
				}
			}
		} else {
			trigger_error('no email sent. Foomo.Monolog smtp not found', E_USER_WARNING);
		}

	}

}
