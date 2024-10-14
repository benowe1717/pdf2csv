<?php

/**
 * Doctrine Data Fixture for User Entity
 *
 * PHP version 8.3
 *
 * @category  DataFixture
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/pdf2csv
 **/

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Doctrine Data Fixture for User Entity
 *
 * PHP version 8.3
 *
 * @category  DataFixture
 * @package   PDF2CSV
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2024 Benjamin Owen
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html#license-text GNU GPLv3
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/pdf2csv
 **/
class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * UserFixtures constructor
     *
     * @param UserPasswordHasherInterface $passwordHasher Password Hasher
     **/
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Load data into database
     *
     * @param ObjectManager $manager Persist data to database
     *
     * @return void
     **/
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // $manager->flush();

        $file = './data/users.csv';

        $row = 1;
        if (($handle = fopen($file, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if ($row === 1) {
                    // This is the header row, no need to parse it
                    $row++;
                    continue;
                }
                $roles = array();

                $email = $data[0];
                $plainPassword = $data[1];
                $roles[] = $data[2];
                $reference = $data[3];

                $user = new User();
                $user->setEmail($email);
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $user,
                    $plainPassword
                );
                $user->setPassword($hashedPassword);
                $user->setRoles($roles);

                $manager->persist($user);
                $manager->flush();

                $ref = "user.{$reference}";
                $this->addReference($ref, $user);

                $row++;
            }
        }
    }
}
