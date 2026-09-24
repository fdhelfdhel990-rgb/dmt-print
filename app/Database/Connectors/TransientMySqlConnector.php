<?php

namespace App\Database\Connectors;

use Exception;
use Illuminate\Database\Connectors\MySqlConnector;
use Illuminate\Support\Str;
use PDO;
use Throwable;

class TransientMySqlConnector extends MySqlConnector
{
    /**
     * @var int
     */
    private const MAX_ATTEMPTS = 3;

    /**
     * Create a new PDO connection.
     *
     * @param  array<int, mixed>  $options
     *
     * @throws Throwable
     */
    public function createConnection($dsn, array $config, array $options): PDO
    {
        [$username, $password] = [
            $config['username'] ?? null, $config['password'] ?? null,
        ];

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            try {
                return $this->createPdoConnection($dsn, $username, $password, $options);
            } catch (Exception $exception) {
                if (! $this->shouldRetryConnection($exception, $attempt)) {
                    throw $exception;
                }

                $this->backoffBeforeRetry($attempt);
            }
        }

        throw $exception;
    }

    /**
     * @throws Throwable
     */
    private function shouldRetryConnection(Throwable $exception, int $attempt): bool
    {
        return $attempt < self::MAX_ATTEMPTS && $this->causedByTransientConnectionFailure($exception);
    }

    private function causedByTransientConnectionFailure(Throwable $exception): bool
    {
        return Str::contains($exception->getMessage(), [
            'php_network_getaddresses',
            'getaddrinfo',
            'Name or service not known',
            'Temporary failure in name resolution',
            'Connection timed out',
            'Connection refused',
            'Network is unreachable',
            'No route to host',
        ]);
    }

    private function backoffBeforeRetry(int $attempt): void
    {
        usleep($this->retryBackoffMicroseconds()[$attempt - 1]);
    }

    /**
     * @return array<int, int>
     */
    protected function retryBackoffMicroseconds(): array
    {
        return [250_000, 750_000];
    }
}
