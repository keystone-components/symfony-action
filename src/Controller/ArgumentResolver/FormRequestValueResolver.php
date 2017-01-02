<?php

namespace Keystone\Symfony\FormRequest\Controller\ArgumentResolver;

use Keystone\Symfony\FormRequest\Request\FormRequest;
use Keystone\Symfony\FormRequest\Request\FormRequestInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class FormRequestValueResolver implements ArgumentValueResolverInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        // Using service location to avoid always instantiating the form factory
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     *
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return $argument->getType() === FormRequestInterface::class;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     *
     * @return FormRequest
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield new FormRequest($request, $this->container->get('form.factory'));
    }
}
