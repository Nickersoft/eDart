<?php
/*
 * Page Name: Search
 * Purpose: Primary search page
 * Last Updated: 6/5/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Import core functionality

//If there is no keyword
if(!isset($_GET["keyword"]))
{
	header("Location:/"); //Go home
	exit;
}

$keyword = $_GET["keyword"];

//Break down the keyword by spaces
$srch = explode(' ', trim(strtolower($keyword)));

HTML::begin();
//If the keyword is blank, just make it 'Search'
if (trim($_GET["keyword"])=="")
{
	Head::make("Search");
}
else //If there is a keyword, show it in the title
{
	Head::make($_GET["keyword"]);
}

Body::begin();

				//Because the search bar is the same on all pages, we have to change it via JavaScript
				$headsearch_script = <<<HSRCH
					<script>
						var hsea = document.getElementById('headsearch');
						hsea.style.color="white";
						hsea.value = "$keyword"
					</script>
HSRCH;

				echo $headsearch_script;

			?>

			<div class="layout-978 uk-container-center">
				<div class="uk-grid">
				
						<?php
							$cnt = 0; //Count of the items found

							//Words to disregard in the search
							$nowords = array("the", "a", "an", "it", "its", "it's");

							//If you can't connect...
							if(mysqli_connect_errno())
							{
								//Throw an error
								echo "Failed to connect: " . mysqli_connect_error();
							}

							$itemarry = array(); //Array of found items

							//Check for any filters
							$filter_array = getFilters($_GET["keyword"]);

							//Make an API call to get all matching items
							$items = new Item(array("action"=>"get", "filter"=>$filter_array));

							//Get info on all of the items
							$items_info = $items->run(true);

							$keyword = trim(removeFilters($keyword));

							//Loop through each item
							foreach($items_info as $item)
							{
								//If there were additional terms instead of just filters
								if($keyword!="")
								{
									//Loop through each property of the item
									foreach($item as $k=>$v)
									{
										//As long as it's not the image binary
										if($k!="image")
										{
											//Get info about the user who owns the current item
											$owner = new User(array("action"=>"get", "id"=>$item["usr"]));
											$ownerInfo = $owner->run(true);

											//Check to make sure the value isn't empty
											if(trim($v)!="")
											{
												//echo $keyword . "," . $v . "<br/>";
												//Returns true if the keyword is in the current item column
												$item_match = (strpos(strtolower($v), strtolower($keyword))!==false);

												//Returns true if the keyword is in the owner's name
												$user_match = (strpos(strtolower($keyword), strtolower($ownerInfo[0]["fname"])))||
															  (strpos(strtolower($keyword), strtolower($ownerInfo[0]["lname"])));

												//If either of these are true
												if($user_match||$item_match)
												{
													//Print the item to the board
													printItem(trim($item["id"]));

													//Increment the item count
													$cnt++;

													//Break out of the parent for loop
													break 1;
												}
											}
										}
									}
								}
								else
								{
									printItem(trim($item["id"]));
								}
							}

							//If no items were found
							if($cnt==0&&$keyword!="")
							{
								//Aplogize
								echo "We're sorry, but it seems no one has posted ";

								//If there was a keyword to begin with
								if(trim($keyword)!="")
								{
									//Append the keyword to the apology string
									echo "'" . $keyword . "'";
								}
								else //If it was blank
								{
									echo " this item "; //Be generic
								}

								//Finish off the apology string with a link to Amazon
								echo " yet! Why not <a target=\"_blank\" href=\"http://www.amazon.com/s/?field-keywords=".urlencode(trim($_GET["keyword"]))."\">buy</a> it?";
							}

							//Removes any filter strings from the input
							function removeFilters($input)
							{
								//First, convert it to lower case
								$input = strtolower($input);

								//Our final string
								$final = "";

								//Then, divide it by spaces
								$break_by_space = explode(' ', $input);

								//Loop through each term
								foreach($break_by_space as $term)
								{
									//Then break it by colons
									$break_by_colon = explode(':', $term);
									$matched = false;
									//Let us make sure this is an actual array, not just one term
									if(count($break_by_colon)>=2)
									{
										//Get the first word. This will be our key.
										$key = 	$break_by_colon[0];

										//Let's see what it is...
										switch(trim($key))
										{
											case "user":
												$matched = true;
												break;
										}

									}


									//If it's not a filter...
									if(!$matched)
									{
										//Add it to the output
										$final .= $term . " ";
									}

								}

								return $final;
							}

							//Returns an array of filters depending on the input
							//(e.g. user:1, category:books, etc)
							function getFilters($input)
							{
								//Initialize the empty array
								$filter_array = array();

								//First, convert it to lower case
								$input = strtolower($input);

								//Then, divide it by spaces
								$break_by_space = explode(' ', $input);

								//Loop through each term
								foreach($break_by_space as $term)
								{
									//Then break it by colons
									$break_by_colon = explode(':', $term);

									//Let us make sure this is an actual array, not just one term
									if(count($break_by_colon)>=2)
									{
										//Get the first word. This will be our key.
										$key = 	$break_by_colon[0];
										$matched = false;

										//Let's see what it is...
										switch(trim($key))
										{
											case "user":
												$key = "usr";
												$matched = true;
												break;
										}

										//This prevents it from matching anything irrelevant
										if($matched)
										{
											//Remove the first element (key)
											array_shift($break_by_colon);

											$value = implode("", $break_by_colon);

											//Merge the key/values with the master filter array
											$filter_array = array_merge($filter_array, array($key=>$value));
										}

									}

								}

								return $filter_array;

							}

							//Prints an item
							function printItem($itemid)
							{

								//Get the item info
								$item = new Item(array("action"=>"get", "filter"=>array("id"=>$itemid)));
								$item_info = $item->run(true);

								//Load them into variables
								$item_img_url 	= "/imageviewer/?id=".$itemid;
								$item_name 		= $item_info[0]["name"];
								$item_desc		= $item_info[0]["description"];
								
								$item_price		= $item_info[0]["emv"];
								$item_adddate	= $item_info[0]["adddate"];
								$item_duedate	= $item_info[0]["duedate"];
								$item_dodue		= ($item_duedate != 0);
								$item_expires	= $item_info[0]["expiration"];
								$item_owner		= $item_info[0]["usr"];

								//Format the due date
								//If the due date isn't this year, include the year
								if(date("Y", $item_duedate)==date("Y"))
								{
									$item_duedate = date("F jS",$item_duedate);
								}
								else
								{
									$item_duedate = date("n/j/Y", $item_duedate);
								}

								//Format the add date
								//Follow the same year rule as the due date
								if(date("Y", $item_adddate)==date("Y"))
								{
									$item_adddate = date("F jS",$item_adddate);
								}
								else
								{
									$item_adddate = date("n/j/Y", $item_adddate);
								}

								//Format the expiration date
								//Again, follow the year rule
								if(date("Y",$item_expires)==date("Y"))
								{
									$item_expires = date("F jS", $item_expires);
								}
								else
								{
									$item_expires = date("n/j/Y", $item_expires);
								}

								//Get the owner's info
								$owner = new User(array("action"=>"get", "id"=>$item_owner));
								$owner_info = $owner->run(true);

								$owner_name		= $owner_info[0]["fname"] . " " . $owner_info[0]["lname"];

								$item_html = <<<ITEM1
							   		<div class="uk-width-1-1 uk-align-center"> 
										<div class="item" onclick="window.location='/view.php?itemid=$itemid&userid=$item_owner + "';">
											<div class="uk-grid uk-grid-preserve reset_padding">
												<div class="uk-width-4-6">
													<div class="header">$item_name </div>
														<div class="description">$item_desc</div>
												</div>
												<div class="uk-width-2-6">
													<div style="background:url('/imageviewer/?id=$itemid&size=medium') no-repeat center center;" class="thumbnail"> 
														<div class="gradient"></div>
													</a>
												</div>
											</div>
										</div>
									</div>					
ITEM1;
								//Print the HTML
								echo $item_html;
							}
						?>
					</div>
			</div>
		<?php
		Body::end();
		HTML::end();
		?>
