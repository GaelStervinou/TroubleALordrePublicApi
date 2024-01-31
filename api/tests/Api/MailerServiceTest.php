<?php

use PHPUnit\Framework\TestCase;
use App\Service\MailerService;

class MailerServiceTest extends TestCase
{
    public function testSendEmail()
    {
        $clientMock = $this->getMockBuilder(\GuzzleHttp\Client::class)->getMock();
        $configMock = $this->getMockBuilder(\SendinBlue\Client\Configuration::class)->getMock();
        $apiInstanceMock = $this->getMockBuilder(\SendinBlue\Client\Api\TransactionalEmailsApi::class)
            ->setConstructorArgs([$clientMock, $configMock])
            ->getMock();

        $options = [
            'emailTo' => 'test@example.com',
            'lastnameTo' => 'Doe',
            'firstnameTo' => 'John',
            'validationToken' => 'abc123',
        ];

        $configMock->method('setApiKey')->willReturnSelf();
        $clientMock->expects($this->once())->method('__call')->willReturn($apiInstanceMock);

        $expectedSendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail();

        $apiInstanceMock->expects($this->once())->method('sendTransacEmail')->willReturn($expectedSendSmtpEmail);
    }
}
