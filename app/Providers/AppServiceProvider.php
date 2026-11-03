<?php

namespace App\Providers;

use App\Models\AppNotification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use PDO;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->createDatabaseIfMissing();
    }

    private function createDatabaseIfMissing(): void
    {
        if (config('database.default') !== 'mysql') {
            return;
        }

        $config = config('database.connections.mysql');
        $database = (string) ($config['database'] ?? '');

        if ($database === '' || ! preg_match('/^[A-Za-z0-9_]+$/', $database)) {
            return;
        }

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;port=%s', $config['host'], $config['port'] ?: 3306),
                (string) $config['username'],
                (string) ($config['password'] ?? ''),
                [PDO::ATTR_TIMEOUT => 3, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (Throwable) {
            // Migrate reports the real MySQL error if the server is not running.
        }
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Blade::directive('pkr', function (string $expression) {
            return "<?php echo \\App\\Support\\Money::pkr($expression); ?>";
        });

        Blade::directive('pct', function (string $expression) {
            return "<?php echo \\App\\Support\\Money::percent($expression); ?>";
        });

        View::composer(['layouts.app', 'layouts.admin'], function ($view): void {
            $count = 0;

            if (auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('app_notifications')) {
                $count = AppNotification::query()
                    ->where('user_id', auth()->id())
                    ->whereNull('read_at')
                    ->count();
            }

            $view->with('unreadNotifications', $count);
        });
    }
}
