<?php namespace App\Http\Controllers\Api\V1\Classes;

	class EbayApi {

		private $appId = "SamPosin-f9a0-4115-a762-61922e7cbab2";
		public  $findingApi;



		public function __construct()
        {
			$this->findingApi= new FindingApi();

        }

	}