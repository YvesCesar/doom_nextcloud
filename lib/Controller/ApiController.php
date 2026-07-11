<?php

declare(strict_types=1);

namespace OCA\Doom\Controller;

use OCA\Doom\AppInfo\Application;
use OCA\Doom\Service\JsDosAccountService;
use OCA\Doom\Service\JsDosClient;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\DataDisplayResponse;
use OCP\AppFramework\Http\DataResponse;
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
		private ?string $userId,
		private JsDosAccountService $accountService,
		private JsDosClient $jsDosClient,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * Return the stored js-dos account for the current user (null if none).
	 */
	#[NoAdminRequired]
	public function getState(): DataResponse
	{
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_UNAUTHORIZED);
		}

		return new DataResponse([
			'account' => $this->accountService->get($this->userId),
		]);
	}

	/**
	 * Validate a js-dos key server-side (self-hosted emulators cannot, due to
	 * CORS), then store and return the resolved account.
	 */
	#[NoAdminRequired]
	public function setKey(string $key = ''): DataResponse
	{
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_UNAUTHORIZED);
		}

		$account = $this->jsDosClient->resolveAccount($key);
		if ($account === null) {
			return new DataResponse(['status' => 'invalid'], Http::STATUS_UNPROCESSABLE_ENTITY);
		}

		$this->accountService->set($this->userId, $account);

		return new DataResponse(['account' => $account]);
	}

	/**
	 * Delete the stored js-dos account for the current user.
	 */
	#[NoAdminRequired]
	public function deleteState(): DataResponse
	{
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_UNAUTHORIZED);
		}

		$this->accountService->delete($this->userId);

		return new DataResponse(['status' => 'ok']);
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

		$appPath = \OC::$server->getAppManager()->getAppPath(Application::APP_ID);
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
