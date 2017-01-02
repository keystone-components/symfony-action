<?php

namespace Keystone\Symfony\FormRequest\Testing\Request;

use Keystone\Symfony\FormRequest\Exception\FormAlreadyHandledException;
use Keystone\Symfony\FormRequest\Exception\FormNotHandledException;
use Keystone\Symfony\FormRequest\Request\FormRequestInterface;
use Mockery;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

class FakeFormRequest implements FormRequestInterface
{
    /**
     * @var Request
     */
    public $request;

    /**
     * @var FormInterface
     */
    public $form;

    /**
     * @var FormView
     */
    public $formView;

    /**
     * @var bool
     */
    public $handled = false;

    /**
     * @param bool $submitted
     * @param bool $valid
     * @param mixed $data
     * @param Request $request
     */
    public function __construct($submitted = false, $valid = false, $data = null, Request $request = null)
    {
        $this->request = $request ?: new Request();

        $this->form = Mockery::mock(FormInterface::class, [
            'isSubmitted' => $submitted,
            'isValid' => $valid,
            'getData' => $data,
        ]);

        $this->formView = Mockery::mock(FormView::class);
    }

    /**
     * {@inheritdoc}
     */
    public function handle($formType, $bindData = null, array $options = [])
    {
        if ($this->handled) {
            throw new FormAlreadyHandledException();
        }

        $this->handled = true;

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

        return $this->formView;
    }

    /**
     * {@inheritdoc}
     */
    private function assertFormHandled()
    {
        if (!$this->handled) {
            throw new FormNotHandledException();
        }
    }
}
