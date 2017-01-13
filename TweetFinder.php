<?php
$tag = $_POST['tag'];
$type = $_POST['type'];

require_once("TwitterOAuth/autoload.php"); 
require_once 'HTTP/Request2.php';
//require 'vendor/autoload.php';
include 'TwitterOAuth/src/TwitterOAuth.php';

use MediaScanner\TwitterOAuth\TwitterOAuth;
use GuzzleHttp\Client;

define('CONSUMER_KEY', '');
define('CONSUMER_SECRET', '');
define('ACCESS_TOKEN', '');
define('ACCESS_TOKEN_SECRET', '');
 
$toa = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
 
$query = array(
  "q" => $tag,
  "result_type" => "recent",
  "lang" => "en"
);
 
$results = $toa->get('search/tweets', $query);
 
$request = new Http_Request2('https://westus.api.cognitive.microsoft.com/text/analytics/v2.0/sentiment');
$url = $request->getUrl();

$headers = array(
    // Request headers
    'Content-Type' => 'application/json',
    'Ocp-Apim-Subscription-Key' => '',
);

$request->setHeader($headers);
$parameters = array(
    // Request parameters
    //'numberOfLanguagesToDetect' => '1',
);

$url->setQueryVariables($parameters);
$request->setMethod(HTTP_Request2::METHOD_POST);

foreach ($results->statuses as $result) {
	$text = $result->text;

  $body = array(
    "documents" => array(array(
      "language" => "en",
      "id" => "string",
      "text" => $text
    ))
  );
  $request->setBody(json_encode($body));
	try
	{	   
    $response = $request->send()->getBody();
    $data = json_decode($response, true);
    $score = $data['documents'][0]["score"];
    echo $text . "<br>" . "The Sentiment Score is: ". $score . "<br>";
    
	}
	catch (HttpException $e)
	{
    	 var_dump($e);
	}
	//break;
}

