<?php

use AI\Models\Image;
use AI\Providers\UploadcareServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application;

$app['debug'] = true;

$app->register(new TwigServiceProvider, [
	'twig.path' => __DIR__ . '/../views'
]);

$app->register(new DoctrineServiceProvider, [
	'db.options' => [
		'driver'   => 'pdo_mysql',
		'host'	   => 'localhost',
		'dbname'   => 'image_upload',
		'user'     => 'homestead',
		'password' => 'secret',
		'charset'  => 'utf8',
	],
]);

$app->register(new UploadcareServiceProvider);

$app->register(new UrlGeneratorServiceProvider);

$app->get('/', function () use ($app) {

	$images = $app['db']->prepare("SELECT * FROM images");
	$images->execute();

	$images = $images->fetchAll(PDO::FETCH_CLASS, Image::class);

	return $app['twig']->render('home.twig');
});

$app->post('upload', function (Request $request) use ($app) {
	var_dump($request);
})->bind('images.store');

$app->run();
