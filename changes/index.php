<!DOCTYPE html>
<html>
	<head>
		<title>eDart | Change Log</title>

		<link rel="stylesheet" type="text/css" media="screen" href="/fonts/Perfect DOS/stylesheet.css">					
		<link rel="stylesheet" type="text/css" href="log.css">
	</head>

	<body>
		<h1>The eDart Changelog</h1>
		<p>
			You may find yourself asking... how did I get here? Why does this page look like sh*t? Am I in 1995? 
			Naw man, you're on the eDart changelog page, and we like to keep it as simple as possible, even if it does
			mean this page will look like a 1983 IBM PC. Here you will find a list of all bug fixes and improvements we've made
			to the site since its launch on June 1, 2014. Hope ya'll enjoy it.
		</p>
		<ul>
			<?php
				//Load the changelog from file
				$lines = file($_SERVER["DOC_ROOT"]."/changes/log.txt");

				//Loop through each line
				for($i = count($lines) - 1; $i >= 0; $i--)
				{
					//Store the line in a variable
					$line = $lines[$i];

					//Divide it by the bars |
					$divide = explode("|", $line);

					$date   = array_shift($divide); //The date is our first object
					$rest   = implode("|", $divide); //The rest is our string

					$log_html = <<<LOG
						<li>
							<span>$date</span> 
							$rest
						</li>
LOG;
					echo $log_html; 
				}

			?>
		</ul>
	</body>
</html>