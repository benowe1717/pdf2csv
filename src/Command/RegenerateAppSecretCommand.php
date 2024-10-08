<?php

/**
 * Symfony Command for regenerating the App Secret
 *
 * PHP version 8.3
 *
 * @category  Command
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://mit-license.org/ MIT
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/pdf2csv
 **/

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Symfony Command for regenerating the App Secret
 *
 * @category  Command
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://mit-license.org/ MIT
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
#[AsCommand(
    name: 'app:regenerate-app-secret',
    description: 'Regenerate the APP_SECRET in case it gets leaked',
)]
class RegenerateAppSecretCommand extends Command
{
    /**
     * RegenerateAppSecretCommand constructor
     **/
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Method to control what happens when running the command
     *
     * @param InputInterface  $input  The optional parameters passed to the command
     * @param OutputInterface $output The returned value of the command
     *
     * @return int
     **/
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $secret = bin2hex(random_bytes(16));

        $r = shell_exec(
            'sed -i -E "s/^APP_SECRET=.{32}$/APP_SECRET=' . $secret . '/" .env'
        );

        $io->success('New APP_SECRET was generated: ' . $secret);

        return COMMAND::SUCCESS;
    }
}
