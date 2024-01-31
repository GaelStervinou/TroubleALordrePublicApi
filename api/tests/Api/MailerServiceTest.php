<?php

use PHPUnit\Framework\TestCase;
use App\Service\MailerService;
use GuzzleHttp\Client;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;

class MailerServiceTest extends TestCase
{
    public function testSendEmail()
    {
        $clientMock = $this->getMockBuilder(Client::class)->getMock();
        $configMock = $this->getMockBuilder(Configuration::class)->getMock();
        $apiInstanceMock = $this->getMockBuilder(TransactionalEmailsApi::class)->disableOriginalConstructor()->getMock();

        $options = [
            'emailTo' => 'test@example.com',
            'lastnameTo' => 'Doe',
            'firstnameTo' => 'John',
            'validationToken' => 'abc123',
        ];

        $configMock->method('setApiKey')->willReturnSelf();
        $clientMock->expects($this->once())->method('__call')->willReturn($apiInstanceMock);

        $expectedSendSmtpEmail = new SendSmtpEmail();

        $apiInstanceMock->expects($this->once())->method('sendTransacEmail')->willReturn($expectedSendSmtpEmail);
    }
}
