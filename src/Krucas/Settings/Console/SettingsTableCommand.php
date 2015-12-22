<?php namespace Krucas\Settings\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class SettingsTableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'settings:table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration for the settings database table';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Illuminate\Foundation\Composer|\Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new settings table command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem                            $files
     * @param \Illuminate\Foundation\Composer|\Illuminate\Support\Composer $composer
     */
    public function __construct(Filesystem $files, $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $fullPath = $this->createBaseMigration();

        $this->files->put($fullPath, $this->files->get(__DIR__.'/stubs/settings.stub'));

        $this->info('Migration created successfully!');

        $this->composer->dumpAutoloads();
    }

    /**
     * Create a base migration file for the table.
     *
     * @return string
     */
    protected function createBaseMigration()
    {
        $name = 'create_settings_table';

        $path = $this->laravel->databasePath().'/migrations';

        return $this->laravel['migration.creator']->create($name, $path);
    }
}
