<?php namespace App\Http\Controllers\Api\V1\Classes;

use Exception;
use BrowserDetect;

class HelperFunctions {

	public static function getBrowser($http_user_agent='')
	{
		$browser="";

		$result_obj=BrowserDetect::detect($http_user_agent);
		$result_arr=$result_obj->toArray();

		if(isset($result_arr['browserFamily']))
			$browser=$result_arr['browserFamily'];

		return $browser;
	}

	public static function getCountriesArrayAvailableInAllApis()
	{
		$country_available_api_info_arr=\Config::get('custom.country_available_api_arr');
		$country_available_api_arr=array_keys($country_available_api_info_arr);

		return $country_available_api_arr;
	}

	public static function separateWordByLastDot($val)
	{

		$first_word = '';
		$middle_word = '';
		$last_word = '';
		$arr = array();
		if ($val != "")
		{
			$val_arr = explode('.', $val);
			if (count($val_arr) > 1)
			{
				for ($i = 0; $i < count($val_arr); $i++)
				{
					if ($i == 0)
					{
						$first_word = $val_arr[$i];
					}
					elseif ($i == count($val_arr) - 1)
					{
						$last_word = $val_arr[$i];
					}
					else
					{
						$middle_word .= $val_arr[$i] . '.';
					}
				}
				$arr[] = $first_word;
				$arr[] = substr($middle_word, 0, -1);
				$arr[] = $last_word;
			}
		}

		return $arr;
	}

	/**
	 * Returns whether image is greyscale or not.
	 *
	 * @param string $image_path
	 *
	 * @param $info
	 *
	 * @return bool
	 */
	public static function  checkGreyScaleImage($image_path,$info)
	{

		//$info = GetImageSize($image_path);
		$imgw = $info[0];
		$imgh = $info[1];

		//echo "checkGreyScaleImage GetImageSize<br>";
		//print_r($info);

		switch ($info[2]) {
			case IMAGETYPE_GIF:
				$image = @ImageCreateFromGif($image_path);
			break;
			case IMAGETYPE_JPEG:
				$image = @ImageCreateFromJpeg($image_path);
			break;
			case IMAGETYPE_PNG:
				$image = @ImageCreateFromPng($image_path);
			break;
			default:
				return false;
		}

		for ($i = 0; $i < $imgw; $i++) {
			for ($j = 0; $j < $imgh; $j++) {
				// get the rgb value for current pixel
				$rgb = @ImageColorAt($image, $i, $j);
				// extract each value for r, g, b
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				// if gray pixels (r=g=b) check next otherwise it's not greyscale
				if ($r != $g || $r != $b) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * cURL
	 *
	 * Standard cURL function to run GET & POST requests
	 *
	 * @param $url
	 * @param string $method
	 * @param null $headers
	 * @param null $postvals
	 *
	 * @return mixed
	 */
    public static function curl($url, $method = 'GET', $headers = null, $postvals = null){

        $ch = curl_init($url);

        if ($method == 'GET'){
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        } else {
            $options = array(
                CURLOPT_HEADER => true,
                CURLINFO_HEADER_OUT => true,
                CURLOPT_VERBOSE => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $postvals,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_TIMEOUT => 3
            );
            curl_setopt_array($ch, $options);
        }

        $response = curl_exec($ch);

        $info = curl_getinfo($ch);

        curl_close($ch);



        return $response;
    }

    public static function curl_with_authentication1($url, $username,$password,$method = 'GET', $headers = null, $postvals = null)
	{

		$ch = curl_init();
		if ($method == 'GET') {
			curl_setopt($ch, CURLOPT_URL, $url);

			curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		}
		else {

			$options = array(
				CURLOPT_HEADER => true,
				CURLINFO_HEADER_OUT => true,
				CURLOPT_VERBOSE => true,
				CURLOPT_HTTPHEADER => $headers,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POSTFIELDS => $postvals,
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_TIMEOUT => 3
			);
			curl_setopt_array($ch, $options);
		}

		$response = curl_exec($ch);
		//'error:' . curl_error($ch);
		curl_close($ch);

		return $response;
	}

    public static function curl_with_authentication($url, $username,$password,$method = 'GET', $headers = null, $postvals = null)
    {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://geoip.maxmind.com/geoip/v2.1/city/8.8.8.8?pretty');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, "110894:93wunhbm6F8a");
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		if (curl_errno($ch)) {
		echo 'Curl error: ' . curl_error($ch);
		throw new Exception('GeoIP Request Failed');
		}
		curl_close($ch);



		return array('info'=>$info,'response'=>$output);
    }

}