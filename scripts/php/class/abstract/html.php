<?php

abstract class HTML
{
	public function begin()
	{
		echo "<!DOCTYPE html><html>";
	}

	public function end()
	{
		echo "</html>";
	}
}

?>