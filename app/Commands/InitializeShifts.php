<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class InitializeShifts extends BaseCommand
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
    protected $name = 'shift:initialize';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Initialize shift assignments for all operators (first-time setup)';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'shift:initialize [start_date] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'start_date' => 'Start date for shift assignments (Y-m-d format, optional, default: today)',
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--dry-run' => 'Preview assignments without making changes',
        '--force' => 'Force re-initialization (will deactivate existing assignments)',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Shift Initialization Service', 'yellow');
        CLI::write('===========================', 'yellow');
        CLI::newLine();

        $dryRun = CLI::getOption('dry-run');
        $force = CLI::getOption('force');
        $startDate = $params[0] ?? date('Y-m-d');

        // Validate date format
        if (!$this->isValidDate($startDate)) {
            CLI::error('Invalid date format. Please use Y-m-d format (e.g., 2025-12-28)');
            return;
        }

        CLI::write("Start date: {$startDate}", 'white');
        CLI::newLine();

        $service = new \App\Libraries\ShiftRotationService();
        $userModel = new \App\Models\UserModel();

        // Get all operators
        $operators = $userModel->where('jabatan', 'petugas')->findAll();

        if (empty($operators)) {
            CLI::error('No operators found in the system!');
            CLI::write('Please create operator users first (jabatan: petugas)', 'yellow');
            return;
        }

        // Check for existing assignments
        $shiftAssignmentModel = new \App\Models\ShiftAssignmentModel();
        $existingShifts = $shiftAssignmentModel->getAllActiveShifts();

        if (!empty($existingShifts) && !$force) {
            CLI::error('Active shift assignments already exist!');
            CLI::write("Found {count($existingShifts)} active shift(s).", 'yellow');
            CLI::newLine();
            CLI::write('Options:', 'yellow');
            CLI::write('  1. Use --force to re-initialize (will deactivate existing assignments)', 'white');
            CLI::write('  2. Use "php spark shift:rotate" to rotate existing shifts instead', 'white');
            return;
        }

        if ($force && !empty($existingShifts)) {
            CLI::write('FORCE MODE - Will deactivate existing assignments', 'red');
            CLI::newLine();
        }

        if ($dryRun) {
            CLI::write('DRY RUN MODE - No changes will be made', 'cyan');
            CLI::newLine();
        }

        // Display assignment preview
        CLI::write('Operators to be assigned:', 'yellow');
        CLI::write('-------------------------', 'yellow');

        foreach ($operators as $index => $operator) {
            $shiftCode = ($index % 3) + 1;
            $endDate = date('Y-m-d', strtotime($startDate . ' +7 days'));
            CLI::write(sprintf(
                "  %d. %s (ID: %d) → Shift %d (%s to %s, 8 days)",
                $index + 1,
                $operator['nama'],
                $operator['id'],
                $shiftCode,
                $startDate,
                $endDate
            ), 'white');
        }

        CLI::newLine();

        // Show distribution
        $distribution = [1 => 0, 2 => 0, 3 => 0];
        foreach ($operators as $index => $operator) {
            $shiftCode = ($index % 3) + 1;
            $distribution[$shiftCode]++;
        }

        CLI::write('Shift distribution:', 'yellow');
        CLI::write("  Shift 1: {$distribution[1]} operator(s)", 'white');
        CLI::write("  Shift 2: {$distribution[2]} operator(s)", 'white');
        CLI::write("  Shift 3: {$distribution[3]} operator(s)", 'white');
        CLI::newLine();

        if ($dryRun) {
            CLI::write('DRY RUN - No changes were made.', 'cyan');
            return;
        }

        // Confirm before proceeding
        if (!CLI::prompt('Proceed with initialization?', ['y', 'n']) === 'y') {
            CLI::write('Initialization cancelled.', 'yellow');
            return;
        }

        // Get current admin user ID (if available from session or config)
        $createdBy = session()->get('user_info')['id'] ?? null;

        // Perform initialization
        CLI::write('Initializing shifts...', 'yellow');
        $result = $service->initializeShifts($startDate, $createdBy);

        CLI::newLine();

        // Display results
        if ($result['success'] > 0) {
            CLI::write("✓ Successfully initialized {$result['success']} shift assignment(s)", 'green');
        }

        if ($result['failed'] > 0) {
            CLI::write("✗ Failed to initialize {$result['failed']} shift assignment(s)", 'red');
            foreach ($result['errors'] as $error) {
                CLI::write("  - {$error}", 'red');
            }
        }

        CLI::newLine();
        CLI::write('Initialization complete!', 'green');
        CLI::write('Operators can now log in and see their assigned shifts.', 'white');
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
