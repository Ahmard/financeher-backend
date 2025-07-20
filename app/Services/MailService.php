<?php

namespace App\Services;

use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;

class MailService extends BaseService
{
    private array $mailData = [];

    public function __construct()
    {
        // Initialize default email settings
        $this->mailData['smtp'] = [
            'host' => config('mail.mailers.smtp.host'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => config('mail.mailers.smtp.password'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
        ];

        $this->mailData['from'] = [
            'address' => config('mail.from.address'),
            'name' => config('mail.from.name')
        ];
    }

    /**
     * Set the recipient of the email
     *
     * @param string $email
     * @param string $name
     * @return $this
     */
    public function setRecipient(string $email, string $name = ''): static
    {
        // $this->mailData['to'][] = ['address' => $email, 'name' => $name];
        $this->mailData['to'][] = ['address' => $email, 'name' => ""];
        return $this;
    }

    /**
     * Set the subject of the email
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject(string $subject): static
    {
        $this->mailData['subject'] = $subject;
        return $this;
    }

    /**
     * Set the body of the email (HTML format)
     *
     * @param string $htmlBody
     * @return $this
     */
    public function setBody(string $htmlBody): static
    {
        $this->mailData['body'] = $htmlBody;
        return $this;
    }

    /**
     * Set the plain text alternative body
     *
     * @param string $plainTextBody
     * @return $this
     */
    public function setAltBody(string $plainTextBody): static
    {
        $this->mailData['alt_body'] = $plainTextBody;
        return $this;
    }

    /**
     * Attach a file to the email
     *
     * @param string $filePath
     * @param string|null $fileName
     * @return $this
     */
    public function addAttachment(string $filePath, ?string $fileName = null): static
    {
        $this->mailData['attachments'][] = ['file' => $filePath, 'name' => $fileName];
        return $this;
    }

    /**
     * @param string $view
     * @param array $data
     * @return static
     */
    public function view(string $view, array $data = []): static
    {
        $this->setBody(view($view, $data));
        return $this;
    }

    /**
     * Queue the email for background sending
     *
     * @return void
     */
    public function send(): void
    {
        try {
            // Dispatch the email job to the queue
            SendEmailJob::dispatch($this->mailData);
            Log::info('Email queued successfully');
        } catch (\Exception $e) {
            Log::error('Failed to queue email: ' . $e->getMessage());
        }
    }
}
