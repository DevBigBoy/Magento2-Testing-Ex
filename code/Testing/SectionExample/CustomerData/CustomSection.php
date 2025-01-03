<?php

namespace Testing\SectionExample\CustomerData;
use Magento\Customer\CustomerData\SectionSourceInterface;
class CustomSection implements SectionSourceInterface
{

    public function getSectionData()
    {
        return [
            'msg' =>'Data from section',
        ];
    }
}
