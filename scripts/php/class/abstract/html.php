<?php

abstract class HTML
{
	public static function begin()
	{
		echo "<!DOCTYPE html><html>";
	}

	public static function end()
	{
		echo "</html>";
	}
}

?>