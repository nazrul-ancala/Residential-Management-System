<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AutoCheckoutVisitors extends Command
{
    protected $signature   = 'visitors:auto-checkout';
    protected $description = 'Auto-checkout visitors still checked-in from previous days and notify their residents.';

    public function handle(): int
    {
        $this->info('Running auto-checkout...');

        try {
            $pdo  = DB::connection()->getPdo();
            $stmt = $pdo->prepare('CALL sp_AutoCheckout_Visitors()');
            $stmt->execute();
            $affected = $stmt->fetchAll(\PDO::FETCH_OBJ);
            $stmt->closeCursor();

            if (empty($affected)) {
                $this->info('No visitors needed auto-checkout.');
                return self::SUCCESS;
            }

            $this->info(count($affected) . ' visitor(s) auto-checked out. Writing notifications...');

            foreach ($affected as $row) {
                $this->writeNotification(
                    (int) $row->resident_id,
                    $row->visitor_name,
                    $row->ic_passport,
                    $row->visit_date,
                    $row->resident_name
                );
                $this->line("  - Notified resident #{$row->resident_id} ({$row->resident_name}) for visitor {$row->visitor_name}");
            }

            Log::info('AutoCheckoutVisitors completed', ['count' => count($affected)]);
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Auto-checkout failed: ' . $e->getMessage());
            Log::error('AutoCheckoutVisitors Error', ['message' => $e->getMessage()]);
            return self::FAILURE;
        }
    }

    private function writeNotification(int $residentId, string $visitorName, string $icPassport, string $visitDate, string $residentName): void
    {
        DB::table('notifications')->insert([
            'id'              => Str::uuid()->toString(),
            'type'            => 'visitor_auto_checkout',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id'   => $residentId,
            'data'            => json_encode([
                'title'        => 'Visitor Auto Checked-Out',
                'message'      => "Your visitor {$visitorName} (IC: {$icPassport}) scheduled on {$visitDate} was automatically checked out at midnight as no checkout was recorded by security.",
                'visitor_name' => $visitorName,
                'ic_passport'  => $icPassport,
                'visit_date'   => $visitDate,
            ]),
            'read_at'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
