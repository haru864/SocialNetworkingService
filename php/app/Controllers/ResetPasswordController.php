<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Http\Request\ResetPasswordRequest;
use Services\ResetPasswordService;

class ResetPasswordController implements ControllerInterface
{
    private ResetPasswordService $resetPasswordService;

    public function __construct(ResetPasswordService $resetPasswordService)
    {
        $this->resetPasswordService = $resetPasswordService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new InvalidRequestMethodException("reset-password request must be 'POST'.");
        }
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            throw new InvalidRequestMethodException("reset-password request must be 'application/json'.");
        }
        $jsonData = file_get_contents('php://input');
        $reqParamMap = json_decode($jsonData, true);
        $request = new ResetPasswordRequest($reqParamMap);
        $action = $request->getAction();
        if ($action === 'send_email') {
            return $this->sendEmail($request);
        } else if ($action === 'reset_password') {
            return $this->resetPassword($request);
        }
    }

    private function sendEmail(ResetPasswordRequest $request): JSONRenderer
    {
        $this->resetPasswordService->sendEmail($request->getUsername(), $request->getEmail());
        return new JSONRenderer(200, []);
    }

    private function resetPassword(ResetPasswordRequest $request): JSONRenderer
    {
        $this->resetPasswordService->resetPassword($request->getNewPassword(), $request->getHash());
        return new JSONRenderer(200, []);
    }
}
