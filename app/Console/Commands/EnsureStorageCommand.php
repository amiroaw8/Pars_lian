<?php

namespace App\Console\Commands;

use App\Services\FileStorage;
use Illuminate\Console\Command;

class EnsureStorageCommand extends Command
{
    protected $signature = 'storage:ensure';

    protected $description = 'آماده‌سازی پوشه‌های آپلود و لینک public/storage برای سرور';

    public function handle(): int
    {
        $this->info('بررسی ذخیره‌سازی فایل‌ها…');

        $result = FileStorage::ensureDeploymentReady();

        if ($result['directories'] !== []) {
            $this->line('پوشه‌های ایجاد‌شده: '.implode(', ', $result['directories']));
        } else {
            $this->line('پوشه‌های آپلود از قبل موجود هستند.');
        }

        if ($result['link']) {
            $this->info('لینک public/storage فعال است.');
        } else {
            $this->warn('لینک public/storage ایجاد نشد — دستی اجرا کنید: php artisan storage:link');
        }

        if ($result['writable']) {
            $this->info('دسترسی نوشتن storage تأیید شد.');
        } else {
            $this->error('پوشه storage قابل نوشتن نیست — روی سرور chmod/chown را تنظیم کنید.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->comment('برای آپلود از موبایل/کامپیوتر، APP_URL را در .env روی آدرس واقعی سایت تنظیم کنید.');
        $this->comment('محدودیت PHP: upload_max_filesize و post_max_size حداقل 10M باشند.');

        return self::SUCCESS;
    }
}
