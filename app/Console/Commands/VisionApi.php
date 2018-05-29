<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VisionApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visionapi:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will download excel from dropbox, processes it and generate csv.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $visionapi=new \App\Helpers\VisionApi();

        $visionapi->init();
    }
}
