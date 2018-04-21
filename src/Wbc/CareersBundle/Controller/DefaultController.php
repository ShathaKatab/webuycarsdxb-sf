<?php

declare(strict_types=1);

namespace Wbc\CareersBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wbc\CareersBundle\Entity\Role;

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
}
