<?php
/**
 * @category  Custom
 * @package   Custom\Status
 * @author    Anton Dudenkoff <anton@dudenkoff.com>
 * @copyright 2019 Custom
 */

namespace Custom\Status\Controller\Index;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;

/**
 * Status save controller
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param Validator $formKeyValidator
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Validator $formKeyValidator,
        CustomerRepository $customerRepository
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        parent::__construct($context);
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * Save status action
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('edit-status');
        }

        $customerId = $this->customerSession->getCustomerId();

        if ($customerId === null) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving your status.'));
            return $this->_redirect('edit-status');
        }

        try {
            $status = $this->getRequest()->getParam('status');

            $customer = $this->getCustomer();
            $customer->setCustomAttribute('status', $status);

            $this->customerRepository->save($customer);

            $this->messageManager->addSuccessMessage(__('Status was saved successfully.'));

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving your status.'));
        }

        return $this->_redirect('edit-status');
    }

    /**
     * Return current customer
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
