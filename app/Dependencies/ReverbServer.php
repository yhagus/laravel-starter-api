<?php

declare(strict_types=1);

namespace App\Dependencies;

use Exception;
use Laravel\Reverb\Contracts\Connection;
use Laravel\Reverb\Loggers\Log;
use Laravel\Reverb\Protocols\Pusher\Server;

final class ReverbServer extends Server
{
    public function open(Connection $connection): void
    {
        try {
            $this->verifyOrigin($connection);
            $connection->touch();
            $this->handler->handle($connection, 'pusher:connection_established');
            //            Log::info('Connection Established', $connection->id());
        } catch (Exception $e) {
            $this->error($connection, $e);
        }
    }

    public function close(Connection $connection): void
    {
        $this->channels
            ->for($connection->app())
            ->unsubscribeFromAll($connection);
        $connection->disconnect();
        //        Log::info('Connection Closed', $connection->id());
    }
}
