<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AdminControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:controller {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a controller in the Admin namespace';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controllerName = $this->argument('name');
        $path = app_path('Admin/Http/Controllers/' . $controllerName . '.php');

        // Check if the directory exists, if not, create it
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true); // Create directory with permissions
        }
        // Check if the file already exists
        if (file_exists($path)) {
            $this->error("Controller already exists!");
            return;
        }


        $stub = file_get_contents(resource_path('stubs/controller.stub'));

        $stub = "<?php

            namespace App\\Admin\\Http\\Controller;
            
            use Illuminate\\Http\\Request;
            
            class {$controllerName} extends Controller
            {
                public function index()
                {
                    return view('admin.{$controllerName}.index');
                }
            }";

        file_put_contents($path, $stub);

        $this->info("Controller created successfully in app/Admin/Http/Controllers/{$controllerName}.php");
    }
}
