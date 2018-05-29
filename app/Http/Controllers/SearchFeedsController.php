<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SearchFeedsController extends Controller
{
    function format_json($json, $html = false, $tabspaces = null)
    {
        $tabcount = 0;
        $result = '';
        $inquote = false;
        $ignorenext = false;

        if ($html) {
            $tab = str_repeat("&nbsp;", ($tabspaces == null ? 4 : $tabspaces));
            $newline = "<br/>";
        } else {
            $tab = ($tabspaces == null ? "\t" : str_repeat(" ", $tabspaces));
            $newline = "\n";
        }

        for($i = 0; $i < strlen($json); $i++) {
            $char = $json[$i];

            if ($ignorenext) {
                $result .= $char;
                $ignorenext = false;
            } else {
                switch($char) {
                    case ':':
                        $result .= $char . (!$inquote ? " " : "");
                        break;
                    case '{':
                        if (!$inquote) {
                            $tabcount++;
                            $result .= $char . $newline . str_repeat($tab, $tabcount);
                        }
                        else {
                            $result .= $char;
                        }
                        break;
                    case '}':
                        if (!$inquote) {
                            $tabcount--;
                            $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
                        }
                        else {
                            $result .= $char;
                        }
                        break;
                    case ',':
                        if (!$inquote) {
                            $result .= $char . $newline . str_repeat($tab, $tabcount);
                        }
                        else {
                            $result .= $char;
                        }
                        break;
                    case '"':
                        $inquote = !$inquote;
                        $result .= $char;
                        break;
                    case '\\':
                        if ($inquote) $ignorenext = true;
                        $result .= $char;
                        break;
                    default:
                        $result .= $char;
                }
            }
        }

        return $result;
    }


	public function getInputSchema()
    {
		$arr=array(
			"title"=>"Search Feed Input Schema",
			"request_type"=>"get",
			"url_parameters"=>array(
				"kw"=>array(
					"type"=>"string",
					"description"=>"Keyword to search",
					"required"=>"true"
                ),
				"rows_per_page"=>array(
					"type"=>"integer",
					"description"=>"Total item",
					"required"=>"true"
                )
			),
		);

		$json=json_encode($arr);

		return $this->format_json($json,true);

    }

    public function getOutputSchema()
    {
		$arr=array(
			"title"=>"Search Feed Output Schema",
			"type"=>"json",
			"properties"=>array(
				"success"=>array(
					"type"=>"boolean",
					"description"=>" if request successful values 0 or 1 ",
                ),
                "errordescription"=>array(
					"type"=>"string",
					"description"=>"If any error, contains error description.",
                ),
				"info"=>array(
					"type"=>"object",
					"description"=>"if success, contains items info",
					"properties"=>array(
						"items"=>array(
							"type"=>"array",
							"properties"=>array(
								"item_id"=>array(
									"type"=>"string",
									"description"=>"Product id",
								),
								"item_title"=>array(
									"type"=>"string",
									"description"=>"Product name",
								),
								"item_url"=>array(
									"type"=>"string",
									"description"=>"Product detail page url",
								),
								"item_price"=>array(
									"type"=>"string",
									"description"=>"Product price",
								),
								"item_image"=>array(
									"type"=>"string",
									"description"=>"Product image url",
								),
								"category_id"=>array(
									"type"=>"string",
									"description"=>"Category id",
								),
								"category_name"=>array(
									"type"=>"string",
									"description"=>"Category name",
								),
								"is_free_shipping"=>array(
									"type"=>"boolean",
									"description"=>"Is shipping free value 0 or 1",
								),
								"store_name"=>array(
									"type"=>"string",
									"description"=>"Store name",
								),
								"store_url"=>array(
									"type"=>"string",
									"description"=>"Store page url",
								),
							)
						)
					)
                ),
			)
		);
		$json=json_encode($arr);

		return $this->format_json($json,true);
    }
}
