<?php

declare(strict_types=1);

namespace Lof\CustomerAvatar\Model\Attribute\Backend;

use \Lof\CustomerAvatar\Model\Source\Validation\Image;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;


class Avatar extends AbstractBackend
{
    /**
     * @param \Magento\Framework\DataObject $object
     *
     * @return $this
     */
    public function beforeSave($object)
    {
        $validation = new Image();
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($attrCode == 'profile_picture') {
            if ($validation->isImageValid('tmpp_name', $attrCode) === false) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The profile picture is not a valid image.')
                );
            }
        }

        return parent::beforeSave($object);
    }
}
