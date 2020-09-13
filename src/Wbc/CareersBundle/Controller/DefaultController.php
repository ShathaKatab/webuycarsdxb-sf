<?php

declare(strict_types=1);

namespace Wbc\CareersBundle\Controller;

use Application\Sonata\MediaBundle\Entity\Media;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Wbc\CareersBundle\Entity\Candidate;
use Wbc\CareersBundle\Entity\Role;
use Wbc\CareersBundle\Form\CandidateType;

/**
 * Class DefaultController.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @CF\Template()
 */
class DefaultController extends Controller
{
    /**
     * @CF\Route("", methods={"GET"})
     *
     * @return array
     */
    public function indexAction()
    {
        return ['roles' => $this->get('doctrine.orm.entity_manager')->getRepository(Role::class)->findActiveRoles()];
    }

    /**
     * @CF\Route("/{slug}",  name="wbc_careers_role_get", methods={"GET"}, requirements={"slug"="[a-z0-9A-Z_\-]+"})
     * @CF\ParamConverter(class="Wbc\CareersBundle\Entity\Role",
     *     options={"repository_method" = "findOneBySlug",
     *     "mapping": {"slug"="slug"}})
     *
     * @param Role $role
     *
     * @return array
     */
    public function detailAction(Role $role)
    {
        return ['role' => $role];
    }

    /**
     * @CF\Route("/{slug}",  name="wbc_careers_role_apply", methods={"POST"}, requirements={"slug"="[a-z0-9A-Z_\-]+"})
     * @CF\ParamConverter(class="Wbc\CareersBundle\Entity\Role",
     *     options={"repository_method" = "findOneBySlug",
     *     "mapping": {"slug"="slug"}})
     *
     * @CF\Template("@WbcCareers/Default/detail.html.twig")
     *
     * @param Role    $role
     * @param Request $request
     *
     * @return array
     */
    public function apply(Role $role, Request $request)
    {
        $candidate = new Candidate($role);
        $form = $this->createForm(CandidateType::class, $candidate);

        $data = $request->request->all();
        $dataUploadedFile = $request->files->all();
        $data['role'] = $role->getId();

        $form->submit(array_merge($data, $dataUploadedFile));

        if ($form->isValid()) {
            $entityManager = $this->get('doctrine.orm.entity_manager');
            /** @var Candidate $candidate */
            $candidate = $form->getData();
            $uploadedFile = $candidate->getUploadedFile();

            if ($uploadedFile) {
                $extension = $uploadedFile->guessExtension();
                $m = microtime(true);
                $realName = sha1(base_convert((int) (floor($m).($m - floor($m)) * 1000000), 10, 36).$uploadedFile->getFilename());
                $name = sprintf('%s.%s', $realName, $extension);
                $uploadedFile->move('/tmp/', $name);

                $media = new Media();
                $media->setContext('default');
                $media->setProviderName('sonata.media.provider.file');
                $media->setBinaryContent(sprintf('/tmp/%s', $name));
                $media->setName($name);
                $media->setMetadataValue('filename', $name);
                $this->get('sonata.media.manager.media')->save($media);

                $candidate->setUploadedCv($media);
            }

            $entityManager->persist($candidate);
            $entityManager->flush();

            //create stuff
            $this->addFlash('success', sprintf('You have successfully applied for the position of %s!', $role->getTitle()));

            return $this->redirectToRoute('wbc_careers_default_index');
        }

        return ['role' => $role, 'form' => $form ? $form->createView() : null];
    }
}
