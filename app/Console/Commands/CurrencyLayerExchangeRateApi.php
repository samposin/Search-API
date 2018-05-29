<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CurrencyLayerExchangeRateApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencylayerexchangerateapi:init {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will download current currency exchange rate and save into db.';

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
        $currencylayerexchangerateapi=new \App\Helpers\CurrencyLayerExchangeRateApi();

        $currencylayerexchangerateapi->init($this->argument('date'));
    }
}
