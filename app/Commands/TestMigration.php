<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestMigration extends BaseCommand
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
    protected $name = 'test:migration';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Test if r_shift_assignments table exists';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'test:migration';

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
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $tables = $db->listTables();

        CLI::write('All tables in database:', 'yellow');
        foreach ($tables as $table) {
            CLI::write('  - ' . $table);
        }

        if (in_array('r_shift_assignments', $tables)) {
            CLI::write('✓ r_shift_assignments table EXISTS!', 'green');
        } else {
            CLI::write('✗ r_shift_assignments table DOES NOT EXIST', 'red');
        }
    }
}
