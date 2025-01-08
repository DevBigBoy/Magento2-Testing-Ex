<?php

namespace Testing\CustomerBypassEmail\Model\Resolver;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;

class UpdateCustomerBypass implements ResolverInterface
{
    private $customerSession;
    private $customerRepository;

    public function __construct(
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $customerId = $this->customerSession->getCustomerId();

        if (!$customerId) {
            throw new GraphQlInputException(__('Customer is not logged in.'));
        }

        $customer = $this->customerRepository->getById($customerId);
        $input = $args['input'];

        if (!empty($input['firstname'])) {
            $customer->setFirstname($input['firstname']);
        }
        if (!empty($input['lastname'])) {
            $customer->setLastname($input['lastname']);
        }
        if (!empty($input['email'])) {
            $customer->setEmail($input['email']);
        }

        $this->customerRepository->save($customer);

        // Manually build full customer response
        $customAttributes = [];
        foreach ($customer->getCustomAttributes() as $attr) {
            $customAttributes[] = [
                'code' => $attr->getAttributeCode(),
                'value' => $attr->getValue()
            ];
        }

        $addresses = [];
        foreach ($customer->getAddresses() as $address) {
            $addresses[] = [
                'firstname' => $address->getFirstname(),
                'lastname' => $address->getLastname(),
                'street' => $address->getStreet(),
                'city' => $address->getCity(),
                'region' => [
                    'region_code' => $address->getRegionCode(),
                    'region' => $address->getRegion()
                ],
                'postcode' => $address->getPostcode(),
                'country_code' => $address->getCountryId(),
                'telephone' => $address->getTelephone()
            ];
        }

        return [
            'customer' => [
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                'suffix' => $customer->getSuffix(),
                'email' => $customer->getEmail(),
                'custom_attributes' => $customAttributes,
                'addresses' => $addresses
            ]
        ];
    }
}
