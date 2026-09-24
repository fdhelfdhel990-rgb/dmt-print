<?php

namespace Tests\Unit;

use App\Database\Connectors\TransientMySqlConnector;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

class TransientMySqlConnectorTest extends TestCase
{
    public function test_transient_dns_connection_exception_is_retried(): void
    {
        $pdo = $this->createMock(PDO::class);
        $connector = new FakeTransientMySqlConnector([
            new PDOException('SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo for mysql.example failed: Name or service not known'),
            $pdo,
        ]);

        $connection = $connector->createConnection('mysql:host=mysql.example;dbname=app', [
            'username' => 'app',
            'password' => 'secret',
        ], []);

        $this->assertSame($pdo, $connection);
        $this->assertSame(2, $connector->attempts);
        $this->assertSame(1, $connector->backoffs);
    }

    public function test_non_transient_connection_exception_is_not_retried(): void
    {
        $connector = new FakeTransientMySqlConnector([
            new PDOException("SQLSTATE[HY000] [1045] Access denied for user 'app'@'%'"),
        ]);

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage('Access denied');

        try {
            $connector->createConnection('mysql:host=mysql.example;dbname=app', [
                'username' => 'app',
                'password' => 'bad-secret',
            ], []);
        } finally {
            $this->assertSame(1, $connector->attempts);
            $this->assertSame(0, $connector->backoffs);
        }
    }

    public function test_transient_connection_retry_has_maximum_attempts(): void
    {
        $connector = new FakeTransientMySqlConnector([
            new PDOException('SQLSTATE[HY000] [2002] Temporary failure in name resolution'),
            new PDOException('SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo failed: Try again'),
            new PDOException('SQLSTATE[HY000] [2002] Network is unreachable'),
        ]);

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage('Network is unreachable');

        try {
            $connector->createConnection('mysql:host=mysql.example;dbname=app', [
                'username' => 'app',
                'password' => 'secret',
            ], []);
        } finally {
            $this->assertSame(3, $connector->attempts);
            $this->assertSame(2, $connector->backoffs);
        }
    }

    public function test_retry_happens_before_any_query_can_be_executed_twice(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->never())->method('prepare');
        $pdo->expects($this->never())->method('exec');

        $connector = new FakeTransientMySqlConnector([
            new PDOException('SQLSTATE[HY000] [2002] getaddrinfo failed: Name or service not known'),
            $pdo,
        ]);

        $connection = $connector->createConnection('mysql:host=mysql.example;dbname=app', [
            'username' => 'app',
            'password' => 'secret',
        ], []);

        $this->assertSame($pdo, $connection);
    }
}

class FakeTransientMySqlConnector extends TransientMySqlConnector
{
    public int $attempts = 0;

    public int $backoffs = 0;

    /**
     * @param  array<int, PDO|PDOException>  $results
     */
    public function __construct(private array $results) {}

    protected function createPdoConnection($dsn, $username, #[\SensitiveParameter] $password, $options): PDO
    {
        $this->attempts++;

        $result = array_shift($this->results);

        if ($result instanceof PDOException) {
            throw $result;
        }

        return $result;
    }

    protected function retryBackoffMicroseconds(): array
    {
        $this->backoffs++;

        return [0, 0];
    }
}
