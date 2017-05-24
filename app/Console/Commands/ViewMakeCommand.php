<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ViewMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:view {view : View path} {--master : Create view as master template} {--layout= : The template that uses for this view}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new blade template view';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $files;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $view = $this->argument('view');
        $options = $this->option();

        if ($this->files->exists($path = $this->getPath($view))) {
            return $this->error("ERROR: $path already exists!");
        }

        if ($options['master'] && !is_null($options['layout'])) {
            return $this->error("ERROR: The master template must not has template define!");
        }

        $this->makeDirectory(dirname($path));
        $this->files->put($path, $this->content($options));

        $this->info("View created successfully.");
    }

    /**
     * Make view file path.
     *
     * @param string $view
     * @return string
     */
    private function getPath($view)
    {
        return './resources/views/' . str_replace('.', '/', $view) . '.blade.php';
    }

    /**
     * Build the directory for the view if necessary.
     *
     * @param $dir
     */
    private function makeDirectory($dir)
    {
        if (!$this->files->isDirectory($dir)) {
            $this->files->makeDirectory($dir, 0755, true, true);
        }
    }

    /**
     * Generate content for blade template view.
     *
     * @param mixed
     * @return string
     */
    private function content($options)
    {
        if ($options['master']) {
            return <<<EOD
<!DOCTYPE html>
<html>
<head>
    <title></title>

    @section('styles')
    @show
</head>
<body>
    @yield('content')

    @section('scripts')
    @show
</body>
</html>
EOD;
        }
        else {
            if (!is_null($options['layout'])) {
                return <<<EOT
@extends('{$options['layout']}')

@section('content')
@endsection

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
@endsection
EOT;
            }
            else {
                return <<<EOD
<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>

</body>
</html>
EOD;
            }
        }
    }
}
