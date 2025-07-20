<?php

namespace App\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;

class SendEmailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected array $mailData;

    /**
     * Create a new job instance.
     *
     * @param array $mailData
     */
    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function handle(): void
    {
        Log::info(sprintf('Executing job(%s)...', $this->job?->getJobId()));

        $mail = new PHPMailer(true);

        try {
            // Set SMTP settings
            $mail->isSMTP();
            $mail->Host = $this->mailData['smtp']['host'];
            $mail->SMTPAuth = config('mail.mailers.smtp.enable_authentication');
            $mail->Username = $this->mailData['smtp']['username'];
            $mail->Password = $this->mailData['smtp']['password'];
            $mail->SMTPSecure = $this->mailData['smtp']['encryption'];
            $mail->Port = $this->mailData['smtp']['port'];

            // Set email sender
            $mail->setFrom($this->mailData['from']['address'], $this->mailData['from']['name']);

            // Set email recipients
            foreach ($this->mailData['to'] as $recipient) {
                $mail->addAddress($recipient['address'], "");
            }

            // Set email subject and body
            $mail->Subject = $this->mailData['subject'];
            $mail->Body = $this->mailData['body'];
            $mail->isHTML(true);

            // Set alternative plain text body
            if (isset($this->mailData['alt_body'])) {
                $mail->AltBody = $this->mailData['alt_body'];
            }

            // Add attachments
            if (isset($this->mailData['attachments'])) {
                foreach ($this->mailData['attachments'] as $attachment) {
                    $mail->addAttachment($attachment['file'], $attachment['name']);
                }
            }

            // Send the email
            $mail->send();
            Log::info('Email sent successfully');
        } catch (Exception $e) {
            Log::info(sprintf('Job(%s) failed', $this->job?->getJobId()));
            Log::error('Failed to send email: ' . $mail->ErrorInfo);
            Log::error($e);
            throw new $e;
        }
    }
}
