<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RotateShifts extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'App';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'shift:rotate';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Automatically rotate expired shift assignments';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'shift:rotate [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--dry-run' => 'Preview what would be rotated without making changes',
        '--force' => 'Force rotation even if no shifts are expired',
        '--date' => 'Start date for new shift assignments (Y-m-d format, default: today)',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Shift Rotation Service', 'yellow');
        CLI::write('=====================', 'yellow');
        CLI::newLine();

        $dryRun = CLI::getOption('dry-run');
        $force = CLI::getOption('force');
        $startDate = CLI::getOption('date') ?? date('Y-m-d');

        // Validate date format
        if (!$this->isValidDate($startDate)) {
            CLI::error('Invalid date format. Please use Y-m-d format (e.g., 2025-12-28)');
            return;
        }

        $service = new \App\Libraries\ShiftRotationService();

        if ($dryRun) {
            CLI::write('DRY RUN MODE - No changes will be made', 'cyan');
            CLI::newLine();
        }

        // Get shifts that need rotation
        $expiredShifts = $service->getExpiringShifts(0); // Already expired

        if (empty($expiredShifts) && !$force) {
            CLI::write('✓ No shifts need rotation at this time.', 'green');
            CLI::newLine();
            CLI::write('Current shift status:', 'yellow');
            $stats = $service->getShiftStatistics();
            CLI::write("  Total active shifts: {$stats['total_active']}", 'white');
            CLI::write("  Expiring tomorrow: {$stats['total_expiring_soon']}", 'white');
            return;
        }

        if ($force) {
            CLI::write('FORCE MODE - Rotating ALL active shifts', 'yellow');
            CLI::newLine();
        }

        // Display what will be rotated
        CLI::write('Shifts to be rotated:', 'yellow');
        $toRotate = $force ? $service->getShiftStatistics()['active_shifts'] : $expiredShifts;

        foreach ($toRotate as $shift) {
            $nextShift = $service->getNextShift($shift['shift_code']);
            CLI::write("  • {$shift['nama']} (ID: {$shift['user_id']}): Shift {$shift['shift_code']} → Shift {$nextShift}", 'white');
        }
        CLI::newLine();

        if ($dryRun) {
            CLI::write('DRY RUN - No changes were made.', 'cyan');
            return;
        }

        // Confirm before proceeding
        if (!$force && !CLI::prompt('Proceed with rotation?', ['y', 'n']) === 'y') {
            CLI::write('Rotation cancelled.', 'yellow');
            return;
        }

        // Perform rotation
        CLI::write('Processing rotation...', 'yellow');

        if ($force) {
            $result = $service->rotateAllShifts($startDate);
        } else {
            $result = $service->autoRotateExpiredShifts($startDate);
        }

        CLI::newLine();

        // Display results
        if ($result['success'] > 0) {
            CLI::write("✓ Successfully rotated {$result['success']} shift(s)", 'green');
        }

        if ($result['failed'] > 0) {
            CLI::write("✗ Failed to rotate {$result['failed']} shift(s)", 'red');
            foreach ($result['errors'] as $error) {
                CLI::write("  - {$error}", 'red');
            }
        }

        CLI::newLine();
        CLI::write('Rotation complete!', 'green');
    }

    /**
     * Validate date format
     *
     * @param string $date
     * @return bool
     */
    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
