<?php

namespace OCA\SecSignID\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\SecSignID\Exceptions\InvalidInputException;
use OCA\SecSignID\Exceptions\SecsignException;


trait Errors {

    protected function handleInvalidInput (Closure $callback) {
        try {
            return new DataResponse($callback());
        } catch(InvalidInputException $e) {
            $message = ['message' => $e->getMessage()];
            return new DataResponse($message, Http::STATUS_BAD_REQUEST);
        }
    }

    protected function handleSecsignException(Closure $callback) {
        try {
            return new DataResponse($callback());
        } catch(SecsignException $e) {
            $message = ['message' => $e->getMessage(), 'code' => $e->getCode()];
            return new DataResponse($message, Http::STATUS_BAD_REQUEST);
        }
    }

}