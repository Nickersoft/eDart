<?php
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

abstract class Lookup
{
	private function match_class_code($class, $code)
	{
		$connect = mysqli_connect(host(), username(), password(), mainDb());
		if($code) {
			$return_array = sqlToArray($connect, "SELECT `text` FROM `lookup` WHERE class=$class AND code=$code");
			if(count($return_array)!=0)
			{
				return $return_array[0]["text"];
			}
			else
			{
				return NULL;
			}
		} else {
			$return_array = sqlToArray($connect, "SELECT * FROM `lookup` WHERE class=$class");
			return $return_array;
		}
	}

	public function Gender($code = NULL)
	{
		return static::match_class_code(3, $code);
	}

	public function Pronoun($code)
	{
		return static::match_class_code(4, $code);
	}

	public function Category($code = NULL)
	{
		return static::match_class_code(1, $code);
	}

	public function Condition($code = NULL)
	{
		return static::match_class_code(2, $code);
	}

	public function Alert($code)
	{
		return static::match_class_code(6, $code);
	}

	public function Error($code)
	{
		return static::match_class_code(5, $code);
	}
}

?>
