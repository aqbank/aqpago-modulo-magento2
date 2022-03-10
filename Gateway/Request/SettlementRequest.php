<?php

namespace Aqbank\Aqpago\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Aqbank\Aqpago\Gateway\Helper\SubjectReader;

class SettlementRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param ConfigInterface $config
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        ConfigInterface $config,
        SubjectReader $subjectReader
    ) {
        $this->config = $config;
        $this->subjectReader = $subjectReader;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $order = $paymentDO->getOrder();

        $payment = $paymentDO->getPayment();

        if (!$payment instanceof OrderPaymentInterface) {
            throw new \LogicException('Order payment should be provided.');
        }

        return [
            'AMOUNT' => $order->getGrandTotalAmount(),
            'TID' => $payment->getAdditionalInformation('Transaction ID')
        ];
    }
}
