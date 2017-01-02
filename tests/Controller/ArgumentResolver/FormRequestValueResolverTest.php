<?php

namespace Keystone\Symfony\FormRequest\Controller\ArgumentResolver;

use Keystone\Symfony\FormRequest\Request\FormRequestInterface;
use Mockery;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class FormRequestValueResolverTest extends \PHPUnit_Framework_TestCase
{
    private $resolver;

    public function setUp()
    {
        $container = new Container();
        $container->set('form.factory', Mockery::mock(FormFactoryInterface::class));

        $this->resolver = new FormRequestValueResolver($container);
    }

    public function testSupportsFormRequestInterfaceArgument()
    {
        $this->assertTrue($this->resolver->supports(
            new Request(),
            new ArgumentMetadata('formRequest', FormRequestInterface::class, false, false, null)
        ));
    }

    public function testResolvesFormRequestInterfaceArgument()
    {
        $formRequest = $this->resolver->resolve(
            new Request(),
            new ArgumentMetadata('formRequest', FormRequestInterface::class, false, false, null)
        );

        $this->assertInstanceOf(FormRequestInterface::class, $formRequest->current());
    }
}
