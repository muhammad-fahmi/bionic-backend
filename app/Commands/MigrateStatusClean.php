<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MigrateStatusClean extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Database';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'migrate:status:clean';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Clear cache lalu menampilkan status migration terbaru';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Clearing cache...', 'yellow');

        // Jalankan command cache:clear
        command('cache:clear');

        CLI::write('Cache cleared.', 'green');
        CLI::newLine();

        CLI::write('Running migrate:status...', 'yellow');

        // Jalankan migrate:status
        command('migrate:status');

        CLI::newLine();
        CLI::write('Done.', 'green');
    }
}
