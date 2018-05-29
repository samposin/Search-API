<?php
	namespace App\Http\Controllers\Api\V1\Helpers;
	use App\ConfiguratorGeneratedJs;

	class PublishersHelper {

	public static function getPublisherInfoByConfiguratorJSUniqueID($configurator_unique_id)
	{
		$publisher_info=array();

		$publisher_info['publisher_id']=0;
		$publisher_info['publisher_name']="";

		if(trim($configurator_unique_id)=="")
		{
			return $publisher_info;
		}

		$configuratorGeneratedJs = ConfiguratorGeneratedJs::with('publisher')->where('configurator_unique_id', '=', $configurator_unique_id)->first();

		if ($configuratorGeneratedJs === null)
		{
			return $publisher_info;
		}

		$configuratorGeneratedJsArray=$configuratorGeneratedJs->toArray();
		$publisher_info['publisher_id']=$configuratorGeneratedJsArray['publisher_id'];
		$publisher_info['publisher_name']=$configuratorGeneratedJsArray['publisher']['name'];

		return $publisher_info;
	}
}