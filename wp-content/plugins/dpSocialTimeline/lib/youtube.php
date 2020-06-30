<?php
error_reporting(E_ALL ^ E_NOTICE);

if(empty($_GET['username']) && empty($_GET['q'])) 
	die('The username / search key is required');
	
$screen_name_data = ($_GET['username'] != "" ? $_GET['username'] : $_GET['q']);
$count = $_GET['count'];

if($count == "" || $count <= 20) 
	$count = 20;

//Include Configuration
require_once (dirname (__FILE__) . '/../../../../wp-load.php');
global $dpSocialTimeline;

require_once 'Google/autoload.php';
require_once ('Google/Service/YouTube.php');


/*
* Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
* Google Developers Console <https://console.developers.google.com/>
* Please ensure that you have enabled the YouTube Data API for your project.
*/
$DEVELOPER_KEY = $dpSocialTimeline['google_api_key'];

$client = new Google_Client();
$client->setDeveloperKey($DEVELOPER_KEY);

// Define an object that will be used to make all API requests.
$youtube = new Google_Service_YouTube($client);

$count_items = 0;
$json_decoded = array();

$json_decoded['responseData']['feed']['link'] = "https://facebook.com/".$screen_name_data;
$json_decoded['responseData']['feed']['entries'] = array();

try {
	
	if($_GET['q']) {
		//SEARCH
		
		// Call the search.list method to retrieve results matching the specified
		// query term.
		$searchResponse = $youtube->search->listSearch('id,snippet', array(
		  'q' => $screen_name_data,
		  'maxResults' => $count,
		  'type'	=> 'video'
		));
		
		// Add each result to the appropriate list, and then display the lists of
		// matching videos, channels, and playlists.
		foreach ($searchResponse['items'] as $searchResult) {
			
			$picture = $searchResult['modelData']['snippet']['thumbnails']['medium'];
			$title = $searchResult['modelData']['snippet']['title'];
			$published = $searchResult['modelData']['snippet']['publishedAt'];
			
			if($title == '') {
				$title = $searchResult['modelData']['snippet']['description'];
			}
					
			if(($title == '' && $picture == '') || (is_numeric($count) && $count_items >= $count)) {
				continue;
			}
			
			$json_decoded['responseData']['feed']['entries'][$count_items]['link'] = 'https://www.youtube.com/watch?v='.$searchResult['modelData']['id']['videoId'];
			$json_decoded['responseData']['feed']['entries'][$count_items]['contentSnippet'] = $title;
			$json_decoded['responseData']['feed']['entries'][$count_items]['content'] = $title;
			$json_decoded['responseData']['feed']['entries'][$count_items]['title'] = $title;
			$json_decoded['responseData']['feed']['entries'][$count_items]['thumbnail'] = $picture;
			$json_decoded['responseData']['feed']['entries'][$count_items]['author'] = $screen_name_data;
			$json_decoded['responseData']['feed']['entries'][$count_items]['publishedDate'] = date("D, d M Y H:i:s O", strtotime($published));
			
			$count_items++;
				
		}
	} else {
		// USERNAME
		
		$getPlaylistId = $youtube->channels->listChannels('contentDetails', array(
		  'forUsername' => $screen_name_data
		));
		
		if($getPlaylistId['modelData']['pageInfo']['totalResults'] == 0) {
			$getPlaylistId = $youtube->channels->listChannels('contentDetails', array(
			  'id' => $screen_name_data
			));
		}
		
		$playlist_id = $getPlaylistId[0]->contentDetails['relatedPlaylists']['uploads'];
		
		$searchResponse = $youtube->playlistItems->listplaylistItems('id,snippet', array(
		  'playlistId' => $playlist_id,
		  'maxResults' => $count
		));
		
		// Add each result to the appropriate list, and then display the lists of
		// matching videos, channels, and playlists.
		foreach ($searchResponse['items'] as $searchResult) {
			/*
			echo '<pre>';
			print_r($searchResult);
			echo '</pre>';
			*/
			$picture = $searchResult['modelData']['snippet']['thumbnails']['medium'];
			$title = $searchResult['modelData']['snippet']['title'];
			$published = $searchResult['modelData']['snippet']['publishedAt'];
			
			if($title == '') {
				$title = $searchResult['modelData']['snippet']['description'];
			}
					
			if(($title == '' && $picture == '') || (is_numeric($count) && $count_items >= $count)) {
				continue;
			}
			
			$json_decoded['responseData']['feed']['entries'][$count_items]['link'] = 'https://www.youtube.com/watch?v='.$searchResult['modelData']['snippet']['resourceId']['videoId'];
			$json_decoded['responseData']['feed']['entries'][$count_items]['contentSnippet'] = $title;
			$json_decoded['responseData']['feed']['entries'][$count_items]['content'] = $title;
			$json_decoded['responseData']['feed']['entries'][$count_items]['title'] = $title;
			$json_decoded['responseData']['feed']['entries'][$count_items]['thumbnail'] = $picture;
			$json_decoded['responseData']['feed']['entries'][$count_items]['author'] = $searchResult['modelData']['snippet']['channelTitle'];
			$json_decoded['responseData']['feed']['entries'][$count_items]['publishedDate'] = date("D, d M Y H:i:s O", strtotime($published));
			
			$count_items++;
				
		}
	}
	
	
} catch (Google_ServiceException $e) {
	$htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
	  htmlspecialchars($e->getMessage()));
} catch (Google_Exception $e) {
	$htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
	  htmlspecialchars($e->getMessage()));
}

header("Content-Type: application/json; charset=UTF-8");
echo json_encode($json_decoded);
?>