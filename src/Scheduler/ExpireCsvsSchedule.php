<?php

/**
 * Symfony Scheduler for Expiring CSVs
 *
 * PHP version 8.4
 *
 * @category  Scheduler
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/pdf2csv
 **/

namespace App\Scheduler;

use App\Message\ExpireCsvs;
use App\Message\RemoveExpiredCsvs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Symfony Scheduler for Expiring CSVs
 *
 * PHP version 8.4
 *
 * @category  Scheduler
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
#[AsSchedule('main')]
final class ExpireCsvsSchedule implements ScheduleProviderInterface
{
    /**
     * ExpireCsvsSchedule constructor
     *
     * @param CacheInterface         $cache         The cache interface
     * @param EntityManagerInterface $entityManager The entity manager interface
     **/
    public function __construct(
        private CacheInterface $cache,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Get the schedule that this Message should be emitted on
     *
     * @return Schedule
     **/
    public function getSchedule(): Schedule
    {
        return (
            new Schedule())
            ->add(
                RecurringMessage::every('5 minutes', new ExpireCsvs()),
                RecurringMessage::cron(
                    '30 12 * * *',
                    new RemoveExpiredCsvs(),
                    new \DateTimeZone('America/New_York')
                )
            );
    }
}
