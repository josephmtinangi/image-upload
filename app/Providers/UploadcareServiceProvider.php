<?php

namespace AI\Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Uploadcare\Api;

class UploadcareServiceProvider implements ServiceProviderInterface
{
	public function register(Application $app)
	{
		$app['uploadcare'] = $app->share(function () use ($app) {
			return new Api('1b677f6e3118e2c80031', 'c0ad0fad8cb709855c62');
		});
	}

	public function boot(Application $app)
	{
		//
	}
}
