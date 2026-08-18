<?php

declare(strict_types=1);

namespace Xsga\BBPHP\EmailSender\Adapter\Resend;

use Psr\Log\LoggerInterface;
use Resend;
use Throwable;
use Xsga\BBPHP\EmailSender\Core\Application\Dto\EmailDataDto;
use Xsga\BBPHP\EmailSender\Core\Application\Services\SendEmailService;
use Xsga\BBPHP\EmailSender\Core\Domain\Exceptions\SendEmailException;

final class ResendEmailService implements SendEmailService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $resendApiKey
    ) {
    }

    public function send(EmailDataDto $emailData): void
    {
        $resend = Resend::client($this->resendApiKey);

        try {
            $result = $resend->emails->send([
                'from'    => "$emailData->senderName <$emailData->sender>",
                'to'      => $emailData->recipients,
                'subject' => $emailData->subject,
                'html'    => $emailData->body,
            ]);

            if ($result->statusCode !== 200) {
                $this->logger->error('Failed to send email using Resend service', [
                    'event' => 'email.resend.send.error',
                    'status_code' => $result->statusCode ?? 'unknown',
                    'message' => $result->message ?? 'unknown'
                ]);
                throw new SendEmailException('Failed to send email using Resend service');
            }

            $this->logger->info('Email sent successfully using Resend service', [
                'event' => 'email.resend.send.success',
                'message_id' => $result->id,
                'sender' => $emailData->sender,
                'recipients' => $emailData->recipients,
                'subject' => $emailData->subject
            ]);
        } catch (Throwable $exception) {
            $errorMsg = 'Failed to send email using Resend service';

            $this->logger->error($errorMsg, [
                'event' => 'email.resend.send.error_sending_email',
                'sender' => $emailData->sender,
                'recipients' => $emailData->recipients,
                'subject' => $emailData->subject,
                'exception_message' => $exception->getMessage(),
                'exception_stack_trace' => $exception->getTraceAsString(),
            ]);

            throw new SendEmailException($errorMsg);
        }
    }
}
