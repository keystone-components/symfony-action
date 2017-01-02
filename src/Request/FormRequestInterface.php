<?php

namespace Keystone\Symfony\FormRequest\Request;

interface FormRequestInterface
{
    /**
     * @param string $formType
     * @param mixed $bindData
     * @param array $options
     *
     * @return bool
     */
    public function handle($formType, $bindData = null, array $options = []);

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * Use this to retrieve the validated data from the form even when you attached `$bindData`.
     *
     * Only by using this method you can mock the form handling by providing a replacement valid value in tests.
     *
     * @return mixed
     */
    public function getData();

    /**
     * Is the bound form valid?
     *
     * @return bool
     */
    public function isValid();

    /**
     * Is the request bound to a form?
     *
     * @return bool
     */
    public function isSubmitted();

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm();

    /**
     * Create the form view for the handled form.
     *
     * Throws exception when no form was handled yet.
     *
     * @return \Symfony\Component\Form\FormViewInterface
     */
    public function createFormView();
}
