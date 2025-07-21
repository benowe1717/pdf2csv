<?php

/**
 * Symfony Message for Support Requests on the app_contact Route
 *
 * PHP version 8.4
 *
 * @category  Message
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/pdf2csv
 **/

namespace App\Message;

/**
 * Symfony Message for Support Requests on the app_contact Route
 *
 * PHP version 8.4
 *
 * @category  Message
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
final class SupportRequestMessage
{
    /**
     * SupportRequestMessage constructor
     *
     * @param array $newMessage The details from the submitted form
     **/
    public function __construct(private array $newMessage)
    {
    }

    /**
     * Getter for $newMessage property
     *
     * @return array
     **/
    public function getContent(): array
    {
        return $this->newMessage;
    }
}
