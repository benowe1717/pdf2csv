<?php

/**
 * Symfony Message Handler for SupportRequestMessage
 *
 * PHP version 8.4
 *
 * @category  MessageHandler
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/pdf2csv
 **/

namespace App\MessageHandler;

use App\Message\SupportRequestMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;

/**
 * Symfony Message Handler for SupportRequestMessage
 *
 * PHP version 8.4
 *
 * @category  MessageHandler
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
#[AsMessageHandler]
final class SupportRequestMessageHandler
{
    private MailerInterface $mailer;

    /**
     * SupportRequestMessageHandler constructor
     *
     * @param MailerInterface $mailer The mailer interface
     **/
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Method actions the data given in the message
     *
     * @param SupportRequestMessage $message The message to handle
     *
     * @return void
     **/
    public function __invoke(SupportRequestMessage $message): void
    {
        $data = $message->getContent();

        $sendEmail = (new TemplatedEmail())
            ->from(
                new Address(
                    'pdf2csv@projecttiy.com',
                    'PDF2CSV Support'
                )
            )
            ->to('support@projecttiy.com')
            ->subject('PDF2CSV Support Request')
            ->htmlTemplate('contact/support_request.html.twig')
            ->context(
                [
                    'full_name' => $data['name'],
                    'email_address' => $data['email'],
                    'message' => $data['message']
                ]
            );
        $this->mailer->send($sendEmail);
    }
}
