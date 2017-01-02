<?php

namespace Keystone\Symfony\FormRequest\Request;

interface FormRequestInterface
{
    public function handle($formType, $bindData = null, array $options = []);

    public function getRequest();

    public function getData();

    public function isValid();

    public function isSubmitted();

    public function getForm();

    public function createFormView();
}
