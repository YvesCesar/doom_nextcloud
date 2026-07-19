<?php

declare(strict_types=1);

namespace OCA\Doom\Service;

/**
 * Thrown when the js-dos cloud API cannot be reached, as opposed to the API
 * answering that a key is invalid.
 */
class JsDosUnavailableException extends \Exception {
}
