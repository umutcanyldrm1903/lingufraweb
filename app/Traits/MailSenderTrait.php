<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\GlobalSetting\app\Models\Setting;

trait MailSenderTrait
{
    private static function isQueable(): bool
    {
        return getSettingStatus('is_queable');
    }

    private static function setMailConfig(): bool
    {
        try {
            if (Cache::has('setting')) {
                $email_setting = Cache::get('setting');
            } else {
                $setting_info = Setting::get();
                $setting = [];
                foreach ($setting_info as $setting_item) {
                    $setting[$setting_item->key] = $setting_item->value;
                }
                $email_setting = (object) $setting;
            }

            $encryption = strtolower(trim((string) $email_setting->mail_encryption));
            if ($encryption === 'none' || $encryption === '') {
                $encryption = null;
            }

            $host = trim((string) $email_setting->mail_host);
            $isLocalHost = in_array(strtolower($host), ['localhost', '127.0.0.1'], true);
            if ($isLocalHost) {
                $encryption = null;
            }
            $username = $email_setting->mail_username;
            $password = $email_setting->mail_password;
            if (!$encryption && $isLocalHost) {
                $username = null;
                $password = null;
            }

            $streamOptions = [];
            if ($isLocalHost) {
                $streamOptions = [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ];
            }

            $useSendmail = $isLocalHost && !$encryption && ((int) $email_setting->mail_port === 25);
            if ($useSendmail) {
                config(['mail.default' => 'sendmail']);
                config(['mail.mailers.sendmail' => [
                    'transport' => 'sendmail',
                    'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
                ]]);
            } else {
                $mailConfig = [
                    'transport' => 'smtp',
                    'host' => $host,
                    'port' => $email_setting->mail_port,
                    'encryption' => $encryption,
                    'username' => $username,
                    'password' => $password,
                    'timeout' => 20,
                    'auto_tls' => $encryption !== null,
                    'stream' => $streamOptions,
                ];

                config(['mail.default' => 'smtp']);
                config(['mail.mailers.smtp' => $mailConfig]);
            }
            config(['mail.from.address' => $email_setting->mail_sender_email]);
            config(['mail.from.name' => $email_setting->mail_sender_name]);

            return true;
        } catch (Exception $e) {
            Log::error('Mail configuration failed', ['error' => $e->getMessage()]);

            return false;
        }
    }
}
