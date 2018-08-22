<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\Command;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Entity\AppointmentReminder;

/**
 * Class AppointmentReminderSmsCommand.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class AppointmentReminderSmsCommand extends ContainerAwareCommand
{
    private $appointmentStatuses = [
        Appointment::STATUS_NEW,
        Appointment::STATUS_CALLBACK,
        Appointment::STATUS_CHECKED_IN,
        Appointment::STATUS_CONFIRMED,
        Appointment::STATUS_INSPECTED,
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('branch:appointment:reminder-sms')
            ->addOption('age', null, InputOption::VALUE_REQUIRED, 'Missed Appointment age in days, default 30 days', 30)
            ->addOption('batch-size', null, InputOption::VALUE_REQUIRED, 'Appointments retrieved from the database at once', 500)
            ->addOption('initial-date', null, InputOption::VALUE_REQUIRED, 'Initial date to remind customers (Y-m-d)', '2018-08-01')
            ->setDescription('Appointment Reminder SMS expecting a response of Yes or Y from the customer');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $batchSize = (int) $input->getOption('batch-size');
        $age = (int) $input->getOption('age');
        $initialDate = new \DateTime($input->getOption('initial-date'));
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        if ($batchSize <= 0) {
            throw new \InvalidArgumentException('Batch size must be greater than zero');
        }

        if ($age <= 0) {
            throw new \InvalidArgumentException('Age in days must be greater than zero');
        }

        if (!$initialDate instanceof \DateTime) {
            throw new \InvalidArgumentException(sprintf('Invalid initial date: %s', $initialDate->format('Y-m-d')));
        }

        $output->writeln(sprintf('Started sending SMS for Missed Appointments after %d days', $age));

        $container = $this->getContainer();
        $templating = $container->get('templating');
        $smsManager = $container->get('wbc.utility.twilio_manager');

        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $container->get('doctrine.orm.default_entity_manager');
        $offset = 0;

        $sqlQuery = 'SELECT a.id, a.name, a.date_booked, a.mobile_number, make.name AS vehicle_make_name, model.name AS vehicle_model_name, a.vehicle_year 
                      FROM appointment a 
                      INNER JOIN vehicle_model model ON a.vehicle_model_id = model.id
                      INNER JOIN vehicle_make make ON model.make_id = make.id
                      INNER JOIN (
                        SELECT d.id AS id, i.appointment_id AS appointment_id
                        FROM deal AS d
                        INNER JOIN inspection AS i ON d.inspection_id = i.id
                      ) AS deal ON deal.appointment_id = a.id
                      LEFT JOIN appointment_reminder ar ON ar.appointment_id = a.id
                      WHERE ar.appointment_id IS NULL
                      AND DATEDIFF(:nowDate, a.date_booked) >= :age
                      AND a.created_at >= :initialDate 
                      AND a.status IN ("'. implode('","', $this->appointmentStatuses) .'")
                      LIMIT :offset, :batchSize
                      ';

        $connection = $entityManager->getConnection();
        $statement = $connection->prepare($sqlQuery);

        do {
            $statement->bindValue(':age', $age, \PDO::PARAM_INT);
            $statement->bindValue(':nowDate', (new \DateTime())->format('Y-m-d'));
            $statement->bindValue(':initialDate', $initialDate->format('Y-m-d'));
            $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $statement->bindValue(':batchSize', $batchSize, \PDO::PARAM_INT);

            $statement->execute();

            $appointmentsData = $statement->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($appointmentsData as $data) {
                $phoneNumber = $data['mobile_number'];

                try {
                    $phoneNumberProto = $phoneNumberUtil->parse($phoneNumber, 'AE');
                    $phoneNumber = $phoneNumberUtil->format($phoneNumberProto, PhoneNumberFormat::E164);
                } catch (NumberParseException $e) {
                    //ignore
                }

                $appointmentReminder = new AppointmentReminder($entityManager->getReference(Appointment::class, $data['id']), $phoneNumber);

                $response = $smsManager->sendSms($phoneNumber, $templating->render('@WbcBranch/appointmentReminderSms.txt.twig', [
                    'appointmentData' => $data,
                ]));


                if ($response instanceof \Twilio\Rest\Api\V2010\Account\MessageInstance) {
                    $entityManager->persist($appointmentReminder);
                    echo '.';
                }
            }

            $entityManager->flush();

            $offset += $batchSize;
        } while ($appointmentsData);

        $output->writeln(sprintf('End sending SMS for Missed Appointments after %d days', $age));
    }
}
