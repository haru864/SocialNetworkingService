<?php

namespace Database\DataAccess\Interfaces;

use Models\EmailVerification;

interface EmailVerificationDAO
{
    public function create(EmailVerification $emailVerification): void;
    public function getByHash(string $hash): ?EmailVerification;
    public function deleteByHash(string $hash): bool;
    public function deleteExpiredHash(): bool;
}
