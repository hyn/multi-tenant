<?php

namespace Hyn\Tenancy\Validators;

use Hyn\Framework\Validators\AbstractValidator;

class TenantValidator extends AbstractValidator
{
    protected $rules = [
        'name'          => ['required', 'min:3'],
        'email'         => ['email', 'required'],
        'customer_no'   => [],
        'administrator' => ['boolean'],
        'reseller_id'   => ['exists:tenants,id'],
        'affiliate_id'  => ['exists:tenants,id'],
    ];
}
