<?php

namespace Somar\NZBN\Control;

use Somar\NZBN\Service\LookupService;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;

/**
 * Controller for the nzbn lookup service
 *
 * @package nzbn
 */
class LookupController extends Controller
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'index',
        'search'
    ];

    /**
     * @param HTTPRequest $request
     * @return HTTPResponse
     */
    public function index(HTTPRequest $request)
    {
        if ($request->isAjax()) {
            $nzbnService = new LookupService();

            $nzbn = $request->getVar('NZBN');

            if (!preg_match('~^\d{13}$~', $nzbn)) {
                return $this->jsonResponse(['error' => 'Bad Request'], 400);
            }

            $data = $nzbnService->get($nzbn);

            return $this->jsonResponse($data);
        }

        return $this->jsonResponse(['error' => 'Bad Request'], 400);
    }

    /**
     * @param HTTPRequest $request
     * @return HTTPResponse
     */
    public function search(HTTPRequest $request)
    {
        if ($request->isAjax()) {
            $nzbnService = new LookupService();

            $query = $request->getVar('query');
            $data = $nzbnService->search($query);

            return $this->jsonResponse($data);
        }

        return $this->jsonResponse(['error' => 'Bad Request'], 400);
    }

    /**
     * @param mixed $data
     * @param int $code
     * @return HTTPResponse
     */
    private function jsonResponse($data, $code = null)
    {
        $response = new HTTPResponse(json_encode($data), $code);
        $response->addHeader('Content-Type', 'application/json');

        return $response;
    }
}
