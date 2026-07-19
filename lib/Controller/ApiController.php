<?php

declare(strict_types=1);

namespace OCA\Doom\Controller;

use OCA\Doom\AppInfo\Application;
use OCP\App\IAppManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\DataDisplayResponse;
use OCP\AppFramework\Http\NotFoundResponse;
use OCP\AppFramework\Http\StreamResponse;
use OCP\IRequest;

/**
 * @psalm-suppress UnusedClass
 */
class ApiController extends Controller
{
	public function __construct(
		string $appName,
		IRequest $request,
		private IAppManager $appManager,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * Serve the doom.jsdos bundle file
	 *
	 * @return DataDisplayResponse
	 */
	#[NoAdminRequired]
	#[NoCSRFRequired]
	public function getDoomBundle(): DataDisplayResponse
	{
		$appPath = $this->appManager->getAppPath(Application::APP_ID);
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

	/**
	 * Serve emulator static files (JS, WASM) via PHP to ensure correct
	 * MIME types and compatibility with Nextcloud installations in subdirectories.
	 *
	 * @param string $filename
	 * @return StreamResponse|NotFoundResponse
	 */
	#[NoAdminRequired]
	#[NoCSRFRequired]
	public function getEmulatorFile(string $filename): StreamResponse|NotFoundResponse
	{
		if (!preg_match('/^[\w\-]+\.(wasm|js|js\.map|js\.symbols)$/', $filename)) {
			return new NotFoundResponse();
		}

		$appPath = $this->appManager->getAppPath(Application::APP_ID);
		$filePath = $appPath . '/js/emulators/' . $filename;

		if (!file_exists($filePath)) {
			return new NotFoundResponse();
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		$contentType = match ($extension) {
			'wasm' => 'application/wasm',
			'js'   => 'application/javascript',
			default => 'application/octet-stream',
		};

		$response = new StreamResponse($filePath);
		$response->setHeaders([
			'Content-Type'  => $contentType,
			'Cache-Control' => 'public, max-age=3600',
		]);

		return $response;
	}
}
