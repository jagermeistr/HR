<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSecurityCredential extends Command
{
    protected $signature = 'mpesa:security-credential';
    protected $description = 'Generate M-Pesa B2C security credential';

    public function handle(): void
    {
        $this->info('M-Pesa Security Credential Generator');
        
        $password = $this->secret('Enter initiator password (sandbox: Safaricom999!*!)');
        
        if (empty($password)) {
            $this->error('Password cannot be empty');
            return;
        }
        
        $securityCredential = base64_encode($password);
        
        $this->info("Generated Security Credential: {$securityCredential}");
        $this->updateEnvFile($securityCredential);
        $this->info('Security credential updated in .env file!');
    }
    
    protected function updateEnvFile(string $credential): void
    {
        $envPath = base_path('.env');
        $content = File::get($envPath);
        
        if (str_contains($content, 'MPESA_B2C_SECURITY_CREDENTIAL')) {
            $content = preg_replace(
                '/MPESA_B2C_SECURITY_CREDENTIAL=.*/',
                "MPESA_B2C_SECURITY_CREDENTIAL={$credential}",
                $content
            );
        } else {
            $content .= "\nMPESA_B2C_SECURITY_CREDENTIAL={$credential}";
        }
        
        File::put($envPath, $content);
    }
}