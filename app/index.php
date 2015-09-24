<?php
/**
 * Author: Grzesiek
 * Date: 22.09.2015 17:24
 */


//	X - przypisane do FO, starsze niż Y dni, nie zamknięte
//	im X większe tym gorzej
//	Z - przypisane do DEV, starsze (ostatnia odpowiedź) niż W dni, nie zamknięte
//	im Z większe tym gorzej
//	Z1 - przypisane do DEV, starsze (czas zgłoszenia - pierwsza wiadomość w konwersacji) niż W1 dni, nie zamknięte
//	im Z1 większe tym gorzej
//	H - nowych konwersacji bez odpowiedzi na FO dłużej niż J godzin
//	im H większe tym gorzej

require_once '../vendor/autoload.php';
require_once 'config-env.php';

$app = new \Slim\Slim();

# fix for setting correct PATH_INFO
$requestPath = parse_url($_SERVER['REQUEST_URI'])['path'];
$env = $app->environment;
$env['PATH_INFO'] = substr($requestPath, 0, strlen($env['SCRIPT_NAME'])) == $env['SCRIPT_NAME']
	? substr_replace($requestPath, '', 0, strlen($env['SCRIPT_NAME'])) : $requestPath ;
# fix end

$app->notFound(function () use ($app) {
	echo json_encode(array('status' => 'error', 'result' => 'Method not found'));
});

$app->get('/assigned-to-fo-by-age', function () use ($app, $config) {

	$helpScout = new HelpScout(HELPSCOUT_API_KEY);

	$parser = new HelpScoutParser($config['assigned-to-fo-by-age'], $helpScout);
	$parser->parseLevels();
	echo GeckoBoardFormatter::counterToRagFormatter($parser->getLevelCounter(), $config['assigned-to-fo-by-age']['levelNames']);


});

$app->run();


