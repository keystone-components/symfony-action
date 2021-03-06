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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->assertFormHandled();

        return $this->form->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $this->assertFormHandled();

        return $this->form->isValid();
    }

    /**
     * {@inheritdoc}
     */
    public function isSubmitted()
    {
        $this->assertFormHandled();

        return $this->form->isSubmitted();
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        $this->assertFormHandled();

        return $this->form;
    }

    /**
     * {@inheritdoc}
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
