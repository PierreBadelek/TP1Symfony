<?php

namespace App;

use App\Message\EnvoyerRappelsMessage;
use App\Message\PurgeAuditLogsMessage;
use App\Message\PurgeEmpruntsMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule as SymfonySchedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule]
class Schedule implements ScheduleProviderInterface
{
    public function __construct(
        private CacheInterface $cache,
    ) {
    }

    public function getSchedule(): SymfonySchedule
    {
        return (new SymfonySchedule())
            ->stateful($this->cache)
            ->processOnlyLastMissedRun(true)

            // Envoi des rappels tous les jours à 8h du matin
            ->add(RecurringMessage::cron('0 8 * * *', new EnvoyerRappelsMessage()))

            // Purge des emprunts anciens tous les jours à 2h du matin
            ->add(RecurringMessage::cron('0 2 * * *', new PurgeEmpruntsMessage()))

            // Purge des logs d'audit de plus de 50 jours, tous les jours à 3h du matin
            ->add(RecurringMessage::cron('0 3 * * *', new PurgeAuditLogsMessage()))
        ;
    }
}
