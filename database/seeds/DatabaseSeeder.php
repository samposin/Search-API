<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountriesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(AdvertisersTableSeeder::class);
        $this->call(PublishersTableSeeder::class);
        $this->call(AdvertiserWidgetsTableSeeder::class);
        $this->call(AdvertiserTypesTableSeeder::class);
        $this->call('WhitelistDomainsTableSeeder');
        $this->call('CurrencyExchangeRatesTableSeeder');
        $this->call('AdvertisersTableSeeder');
        $this->call('AdvertiserSearchDefaultsTableSeeder');
        $this->call('ConfiguratorGeneratedJsTableSeeder');
    }
}
