<?php

namespace Keystone\Symfony\FormRequest\Request;

use Mockery;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

class FormRequestTest extends \PHPUnit_Framework_TestCase
{
    private $request;
    private $formFactory;
    private $formRequest;

    public function setUp()
    {
        $this->request = new Request();

        $this->form = Mockery::mock(FormInterface::class, [
            'handleRequest' => null,
            'isSubmitted' => true,
            'isValid' => true,
            'getData' => null,
            'getName' => 'test',
        ]);

        $this->formFactory = Mockery::mock(FormFactoryInterface::class, [
            'create' => $this->form,
        ]);

        $this->formRequest = new FormRequest($this->request, $this->formFactory);
    }

    public function testGetRequest()
    {
        $this->assertSame($this->request, $this->formRequest->getRequest());
    }

    public function testHandle()
    {
        $data = ['field' => 'value'];
        $options = ['option' => 'value'];

        $this->formFactory->shouldReceive('create')
            ->with('FormType', $data, $options)
            ->andReturn($this->form)
            ->once();

        $this->form->shouldReceive('handleRequest')
            ->with($this->request)
            ->once();

        $this->form->shouldReceive('isSubmitted')
            ->andReturn(true);

        $this->form->shouldReceive('isValid')
            ->andReturn(true);

        $this->assertTrue($this->formRequest->handle('FormType', $data, $options));
    }

    public function testHandleReturnsFalseWhenNotSubmitted()
    {
        $this->form->shouldReceive('isSubmitted')
            ->andReturn(false);

        $this->form->shouldReceive('isValid')
            ->andReturn(true);

        $this->assertFalse($this->formRequest->handle('FormType'));
    }

    public function testHandleReturnsFalseWhenNotValid()
    {
        $this->form->shouldReceive('isSubmitted')
            ->andReturn(true);

        $this->form->shouldReceive('isValid')
            ->andReturn(false);

        $this->assertFalse($this->formRequest->handle('FormType'));
    }

    /**
     * @expectedException Keystone\Symfony\FormRequest\Exception\FormAlreadyHandledException
     */
    public function testCanOnlyHandleOnce()
    {
        $this->formRequest->handle('FormType');
        $this->formRequest->handle('FormType');
    }

    public function testGetData()
    {
        $this->form->shouldReceive('getData')
            ->andReturn('data');

        $this->formRequest->handle('FormType');

        $this->assertSame('data', $this->formRequest->getData());
    }

    /**
     * @expectedException Keystone\Symfony\FormRequest\Exception\FormNotHandledException
     */
    public function testGetDataThrowsExceptionIfNotHandled()
    {
        $this->formRequest->getData();
    }

    public function testIsValid()
    {
        $this->form->shouldReceive('isValid')
            ->andReturn(false);

        $this->formRequest->handle('FormType');

        $this->assertFalse($this->formRequest->isValid());
    }

    /**
     * @expectedException Keystone\Symfony\FormRequest\Exception\FormNotHandledException
     */
    public function testIsValidThrowsExceptionIfNotHandled()
    {
        $this->formRequest->isValid();
    }

    public function testIsSubmitted()
    {
        $this->form->shouldReceive('isSubmitted')
            ->andReturn(false);

        $this->formRequest->handle('FormType');

        $this->assertFalse($this->formRequest->isSubmitted());
    }

    /**
     * @expectedException Keystone\Symfony\FormRequest\Exception\FormNotHandledException
     */
    public function testIsSubmittedThrowsExceptionIfNotHandled()
    {
        $this->formRequest->isSubmitted();
    }

    public function testGetForm()
    {
        $this->formRequest->handle('FormType');

        $this->assertSame($this->form, $this->formRequest->getForm());
    }

    /**
     * @expectedException Keystone\Symfony\FormRequest\Exception\FormNotHandledException
     */
    public function testGetFormThrowsExceptionIfNotHandled()
    {
        $this->formRequest->getForm();
    }

    public function testCreateFormView()
    {
        $this->formRequest->handle('FormType');

        $formView = Mockery::mock(FormView::class);
        $this->form->shouldReceive('createView')
            ->andReturn($formView);

        $this->assertSame($formView, $this->formRequest->createFormView());
    }

    /**
     * @expectedException Keystone\Symfony\FormRequest\Exception\FormNotHandledException
     */
    public function testCreateFormViewThrowsExceptionIfNotHandled()
    {
        $this->formRequest->createFormView();
    }
}
