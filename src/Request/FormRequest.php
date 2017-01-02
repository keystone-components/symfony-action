<?php

namespace Keystone\Symfony\FormRequest\Request;

use Keystone\Symfony\FormRequest\Exception\FormAlreadyHandledException;
use Keystone\Symfony\FormRequest\Exception\FormNotHandledException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormRequest implements FormRequestInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var FormFactoryInterface;
     */
    private $formFactory;

    /**
     * @var FormInterface;
     */
    private $form;

    /**
     * @param Request $request
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(Request $request, FormFactoryInterface $formFactory)
    {
        $this->request = $request;
        $this->formFactory = $formFactory;
    }

    /**
     * @param string $formType
     * @param mixed $bindData
     * @param array $options
     *
     * @return bool
     */
    public function handle($formType, $bindData = null, array $options = [])
    {
        if ($this->form !== null) {
            throw new FormAlreadyHandledException($this->form->getName());
        }

        $this->form = $this->formFactory->create($formType, $bindData, $options);
        $this->form->handleRequest($this->request);

        return $this->form->isSubmitted() && $this->form->isValid();
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Use this to retrieve the validated data from the form even when you attached `$bindData`.
     *
     * Only by using this method you can mock the form handling by providing a replacement valid value in tests.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->assertFormHandled();

        return $this->form->getData();
    }

    /**
     * Is the bound form valid?
     *
     * @return bool
     */
    public function isValid()
    {
        $this->assertFormHandled();

        return $this->form->isValid();
    }

    /**
     * Is the request bound to a form?
     *
     * @return bool
     */
    public function isSubmitted()
    {
        $this->assertFormHandled();

        return $this->form->isSubmitted();
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm()
    {
        $this->assertFormHandled();

        return $this->form;
    }

    /**
     * Create the form view for the handled form.
     *
     * Throws exception when no form was handled yet.
     *
     * @return \Symfony\Component\Form\FormViewInterface
     */
    public function createFormView()
    {
        $this->assertFormHandled();

        return $this->form->createView();
    }

    private function assertFormHandled()
    {
        if ($this->form === null) {
            throw new FormNotHandledException();
        }
    }
}
