# BBPHP Email Sender Resend Adapter

This package provides a Resend implementation of the email sending contract defined by the BBPHP Email Sender core library. It allows applications to send transactional or notification emails through Resend without depending on provider-specific logic in the business layer.

## Overview

The project exposes a concrete adapter named `ResendEmailService`, which implements the `SendEmailService` interface from `xsga/bbphp-emailsender-core`.

The adapter:

- sends emails through the Resend API
- accepts a standard `EmailDataDto` object
- logs success and failure events through PSR-3 logging
- throws a domain-level `SendEmailException` when the sending process fails

## Requirements

- PHP 8.4 or newer
- Composer
- A valid Resend API key
- The core package `xsga/bbphp-emailsender-core`

## Installation

```bash
composer require xsga/bbphp-emailsender-resend-adapter
```

## Usage

```php
<?php

use Psr\Log\NullLogger;
use Xsga\BBPHP\EmailSender\Adapter\Resend\ResendEmailService;
use Xsga\BBPHP\EmailSender\Core\Application\Dto\EmailDataDto;

$emailData = new EmailDataDto();
$emailData->sender = 'noreply@my-domain.com';
$emailData->senderName = 'Support Team';
$emailData->recipients = ['customer@example.com'];
$emailData->subject = 'Welcome to our platform';
$emailData->body = '<h1>Hello!</h1><p>Thank you for joining us.</p>';

$emailService = new ResendEmailService(
    new NullLogger(),
    're_xxxxxxxxxxxxxxxxxxxxxxxxx'
);

$emailService->send($emailData);
```

## Email payload

The adapter builds the Resend payload using the fields required by the core DTO:

- `sender`
- `senderName`
- `recipients`
- `subject`
- `body`

The message is sent as an HTML email using the Resend `emails->send()` call.

## Error handling

If Resend returns a non-success status code or an exception is thrown during the request, the adapter logs the failure and raises `SendEmailException` so the calling code can handle it consistently.

## License

This project is licensed under the MIT License.

## Author

Parker

## Repository

- GitHub: https://github.com/xsga/BBPHP-EmailSender-Resend-Adapter
- Composer package: `xsga/bbphp-emailsender-resend-adapter`