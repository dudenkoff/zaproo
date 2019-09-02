<?php
/**
 * @category  Custom
 * @package   Custom\Status
 * @author    Anton Dudenkoff <anton@dudenkoff.com>
 * @copyright 2019 Custom
 */

namespace Custom\Status\Block;

use Magento\Customer\Model\Session;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class EditStatus
 */
class EditStatus extends Template
{
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param CustomerRepository $customerRepository
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        CustomerRepository $customerRepository,
        Session $customerSession
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Return the save action url
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getUrl('edit-status/index/save');
    }

    /**
     * Return customer status
     *
     * @return string
     */
    public function getStatus()
    {
        try {
            $statusAttribute = $this->getCustomer()
                ->getCustomAttribute('status');
        } catch (\Exception $exception) {
            return '';
        }

        return $statusAttribute ? $statusAttribute->getValue() : '';
    }

    /**
     * Return customer
     *
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getCustomer()
    {
        $customerId = $this->customerSession->getCustomerId();
        $customer = $this->customerRepository->getById($customerId);

        return $customer;
    }
}