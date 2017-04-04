<?php

namespace Wbc\UtilityBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class OptionsAnnotationListener.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service()
 */
class OptionsAnnotationListener
{
    private $annotationsReader;
    /**
     * @param AnnotationReader $annotationsReader
     *
     * @DI\InjectParams({
     * "annotationsReader" = @DI\Inject("annotations.reader")
     * })
     */
    public function __construct(AnnotationReader $annotationsReader)
    {
        $this->annotationsReader = $annotationsReader;
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @DI\Observe("kernel.controller")
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $request = $event->getRequest();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony2 but it may happen.
         * If it is a class, it comes in array format
         *
         */
        if (!is_array($controller)) {
            return;
        }

        list($controllerObject, $methodName) = $controller;
        $optionsAnnotation = 'Wbc\UtilityBundle\Annotation\Options';

        $controllerReflectionObject = new \ReflectionObject($controllerObject);
        $reflectionMethod = $controllerReflectionObject->getMethod($methodName);
        /**@var \Wbc\UtilityBundle\Annotation\Options $methodAnnotation*/
        $methodAnnotation = $this->annotationsReader->getMethodAnnotation($reflectionMethod, $optionsAnnotation);

        if ($methodAnnotation) {
            $request->attributes->set($methodAnnotation->getName(), $methodAnnotation->setRequestParams($request->query->all()));
        }
    }
}
