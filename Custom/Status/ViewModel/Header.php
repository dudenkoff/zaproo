<?php
/**
 * @category  Custom
 * @package   Custom\Status
 * @author    Anton Dudenkoff <anton@dudenkoff.com>
 * @copyright 2019 Custom
 */

namespace Custom\Status\ViewModel;

use Magento\Customer\Model\Session;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Header
 */
class Header implements ArgumentInterface
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
     * @param CustomerRepository $customerRepository
     * @param Session $customerSession
     */
    public function __construct(
        CustomerRepository $customerRepository,
        Session $customerSession
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
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
