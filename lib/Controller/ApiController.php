<?php

declare(strict_types=1);

namespace OCA\Doom\Controller;

use OCA\Doom\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\DataDisplayResponse;

/**
 * @psalm-suppress UnusedClass
 */
class ApiController extends Controller
{
	/**
	 * Serve the doom.jsdos bundle file
	 *
	 * @return DataDisplayResponse
	 */
	#[NoAdminRequired]
	#[NoCSRFRequired]
	public function getDoomBundle(): DataDisplayResponse
	{
		$appPath = \OC::$server->getAppManager()->getAppPath(Application::APP_ID);
		$bundlePath = $appPath . '/js/doom.jsdos';
		
		$data = file_get_contents($bundlePath);
		
		$response = new DataDisplayResponse(
			$data,
			Http::STATUS_OK,
			['Content-Type' => 'application/octet-stream']
		);
		$response->addHeader('Cache-Control', 'public, max-age=3600');
		
		return $response;
	}
}
