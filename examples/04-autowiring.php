<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PerfectApp\Container\Container;

interface MailerInterface
{
    public function send(string $to, string $subject);
}

class SmtpMailer implements MailerInterface
{
    public function send(string $to, string $subject): true
    {
        echo "Sending email to $to with subject: $subject\n";
        return true;
    }
}

class NotificationService
{
    public MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notify(string $email, string $message)
    {
        return $this->mailer->send($email, "Notification: $message");
    }
}

$container = new Container(true);

// The fix allows this to work: interface bound to concrete class
$container->set(MailerInterface::class, SmtpMailer::class);

try {
    // Container will automatically:
    // 1. Detect NotificationService needs MailerInterface
    // 2. Find that MailerInterface is set to SmtpMailer
    // 3. Build SmtpMailer instance (thanks to the fix)
    // 4. Inject it into NotificationService
    $service = $container->get(NotificationService::class);

    // Test it works
    $service->notify('user@example.com', 'Your account was created!');

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
