<?php

namespace App\Service;

use App\Entity\AuditLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class AuditLogService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private RequestStack $requestStack
    ) {}

    public function log(string $action, string $entite, ?int $entiteId, ?string $details = null): void
    {
        $auditLog = new AuditLog();
        $auditLog->setDateAction(new \DateTime());
        $auditLog->setUser($this->security->getUser());
        $auditLog->setAction($action);
        $auditLog->setEntite($entite);
        $auditLog->setEntiteId($entiteId);
        $auditLog->setDetails($details);

        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $auditLog->setIpAddress($request->getClientIp());
        }

        $this->entityManager->persist($auditLog);
        $this->entityManager->flush();
    }
}
