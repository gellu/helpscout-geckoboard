<?php
/**
 * Author: Grzesiek
 * Date: 13.10.2015 18:07
 */

require_once '../vendor/autoload.php';
require_once 'config-env.php';

$app = new \Slim\Slim($config);
$app->config = $config;

# fix for setting correct PATH_INFO
$requestPath = parse_url($_SERVER['REQUEST_URI'])['path'];
$env = $app->environment;
$env['PATH_INFO'] = substr($requestPath, 0, strlen($env['SCRIPT_NAME'])) == $env['SCRIPT_NAME']
	? substr_replace($requestPath, '', 0, strlen($env['SCRIPT_NAME'])) : $requestPath ;
# fix end

$app->notFound(function () use ($app) {
	echo "Not found\n";
});

$argv = $GLOBALS['argv'];
array_shift($GLOBALS['argv']);
$pathInfo = '/' . implode('/', $argv);
$app->environment = Slim\Environment::mock([
	'PATH_INFO'   => $pathInfo
]);

$cfg = $config['counter']['assigned-to-fo-by-age'];

$app->get('/log-assigned-to-fo-by-age', function() use ($app, $cfg) {

	$helpScout = new HelpScout(HELPSCOUT_API_KEY);

	$parser = new HelpScoutParser($cfg, $helpScout);
	$parser->parseLevels();
	$parser->getLevelCounter()['critical'];
	$conversationNumbers = $parser->getConversationsNumbers();

	$msg = [
		'date' 		=> date('l', time()),
		'msg'		=> count($conversationNumbers['critical']),
		'numbers'	=> implode(',', $conversationNumbers['critical'])
	];

	$logger = new Logger('log-assigned-to-fo-by-age.log');
	$logger->log($msg);
});

$app->run();