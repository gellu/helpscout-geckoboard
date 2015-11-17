<?php
/**
 * Author: Grzesiek
 * Date: 22.09.2015 17:24
 */

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

foreach($config['counter'] as $route => $cfg)
{
	$app->get('/'. $route, function () use ($app, $cfg)
	{
		$helpScout = new HelpScout(HELPSCOUT_API_KEY);

		$parser = new HelpScoutParser($cfg, $helpScout);
		$parser->parseLevels();
		echo GeckoBoardFormatter::counterToRagFormatter($parser->getLevelCounter(), $cfg['levelNames']);


	});
}

$app->get('/no-answer', function () use ($app, $config)
{
	$helpScout = new HelpScout(HELPSCOUT_API_KEY);

	$parser = new HelpScoutParser($config['custom']['no-answer'], $helpScout);
	$parser->parseLevelsUnassigned();
	echo GeckoBoardFormatter::counterToRagFormatter($parser->getLevelCounter(), $config['custom']['no-answer']['levelNames']);


});

$app->get('/user-happiness/:type', function ($type) use ($app, $config)
{
	$helpScout = new HelpScout(HELPSCOUT_API_KEY);

	$resp = $helpScout->getHappinessRating($config['custom']['user-replies']['mailboxId'], $type);
	echo GeckoBoardFormatter::happinessToNumberTextFormatter($resp);

});

$app->get('/log-assigned-to-fo-by-age', function () use ($app, $config)
{
	$logger = new Logger('log-assigned-to-fo-by-age.log');
	echo GeckoBoardFormatter::logToList(array_reverse($logger->getLogFileContents()));
});

$app->get('/log-support-reply', function () use ($app, $config)
{
	$logger = new Logger('log-support-reply.log');
	echo GeckoBoardFormatter::logToList(array_reverse($logger->getLogFileContents()));
});

$app->run();


