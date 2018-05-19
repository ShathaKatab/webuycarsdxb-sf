<?php

declare(strict_types=1);

namespace Wbc\CareersBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Wbc\CareersBundle\Entity\Candidate;
use Wbc\UserBundle\Entity\User;
use Wbc\UtilityBundle\MailerManager;

/**
 * Class CandidateListener.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\DoctrineListener(
 *     events = {"postPersist"},
 *     connection = "default",
 *     lazy = true,
 *     priority = 0,
 * )
 */
class CandidateListener
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MailerManager
     */
    private $mailerManager;

    /**
     * CandidateListener constructor.
     *
     * @param EntityManager $entityManager
     * @param MailerManager $mailerManager
     *
     * @DI\InjectParams({
     *     "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *     "mailerManager" = @DI\Inject("wbc.utility.mailer_manager")
     * })
     */
    public function __construct(EntityManager $entityManager, MailerManager $mailerManager)
    {
        $this->mailerManager = $mailerManager;
        $this->entityManager = $entityManager;
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Candidate) {
            return;
        }

        $editors = $this->entityManager->getRepository(User::class)->findAllByRole('ROLE_CAREERS_EDITOR');
        $emails = [];

        /** @var User $editor */
        foreach ($editors as $editor) {
            $emails[] = $editor->getEmail();
        }

        if ($emails) {
            try {
                $this->mailerManager->sendByTemplate(
                    $emails,
                    sprintf('New Application for Role: %s', $object->getRole()->getTitle()),
                    '@WbcCareers/newApplicationEmail.txt.twig',
                    ['candidate' => $object]
                );
            } catch (\Exception $e) {
//                ignore
            }
        }
    }
}
