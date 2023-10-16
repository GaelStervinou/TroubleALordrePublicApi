<?php

namespace App\Service;

use Exception;
use GuzzleHttp\Client;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;

class MailerService {
    public const VERIFY_ACCOUNT_TEMPLATE_ID = 8;
    public const ISSUE_TEMPLATE_ID = 4;
    public const FORGOTTEN_PASSWORD_TEMPLATE_ID = 2;
    public const PROPOSAL_RONEUR_ROLE_TEMPLATE_ID = 3;

    public static function sendEmail(array $options, int $templateId = 2): void
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['SEND_IN_BLUE_API_KEY']);


        $apiInstance = new TransactionalEmailsApi(
            new Client(),
            $config
        );

        $sendSmtpEmail = new SendSmtpEmail();

        $sendSmtpEmail['to'] = [[
            'email'=> $options['emailTo'],
            'name'=> $options['lastnameTo'],
            'PRENOM' => $options['firstnameTo'],
        ]];
        $sendSmtpEmail['params'] = [
            'EMAIL'=> $options['emailTo'],
            'NOM'=> $options['lastnameTo'],
            'PRENOM' => $options['firstnameTo'],
        ];

        //TODO sortir la génération du token dans le controller directement
        if ($templateId === self::VERIFY_ACCOUNT_TEMPLATE_ID) {
            if (!isset($options['validationToken'])) {
                throw new Exception('Validation token is required');
            }
            $lienValidation = $_ENV['APP_URL'] . '/users/verify-account/' . $options['emailTo'] . '/' . $options['validationToken'] . '/';
        }

        $addToParam = match ($templateId) {
            self::VERIFY_ACCOUNT_TEMPLATE_ID => ['LIEN_VALIDATION' => $lienValidation],
            self::ISSUE_TEMPLATE_ID => ['LIEN_ISSUE' => $options['issueLink']],
            self::FORGOTTEN_PASSWORD_TEMPLATE_ID => ['RESET_TOKEN' => $options['resetToken']],
            self::PROPOSAL_RONEUR_ROLE_TEMPLATE_ID => ['LIEN_PROPOSITION' => $options['proposalLink']],
            default => throw new Exception('Template ID not found'),
        };

        $sendSmtpEmail['params'] = array_merge($sendSmtpEmail['params'], $addToParam);
        $sendSmtpEmail['templateId'] = $templateId;

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
        } catch (Exception $e) {
            throw new Exception('Exception when calling TransactionalEmailsApi->sendTransacEmail: ' . $e->getMessage());
        }
    }

}