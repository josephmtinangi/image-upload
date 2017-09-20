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

	return $app['twig']->render('home.twig', [
		'images' => $images,
	]);

})->bind('home');

$app->post('images', function (Request $request) use ($app) {
	if ($request->get('file_id') === '' ) {
		return $app->redirect($app['url_generator']->generate('home'));
	}

	$file = $app['uploadcare']->getFile($request->get('file_id'));

	$image = $app['db']->prepare("
		INSERT INTO images (hash, url)
		VALUES(:hash, :url)
	");

	$image->execute([
		'hash' => bin2hex(random_bytes(20)),
		'url'  => $file->getUrl(),
	]);

	return $app->redirect($app['url_generator']->generate('home'));

})->bind('images.store');

$app->run();
