<?php

namespace App\Console\Commands;

use App\Services\PasswordResetService;
use Illuminate\Console\Command;

class DeleteExpiredPasswordResetTokenCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-expired-password-reset-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired password reset tokens';

    /**
     * Execute the console command.
     */
    public function handle(PasswordResetService $service)
    {
        $this->commentScoped('Deleting expired password reset tokens');
        $service->repository()->deleteExpiredTokens();
        $this->infoScoped('Deleted expired password reset tokens');
        return Command::SUCCESS;
    }
}
