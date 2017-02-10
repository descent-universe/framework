<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Database;


use Descent\Contracts\DatabaseInterface;
use Descent\Database\Contracts\DatabaseStatementInterface;

class Connection implements DatabaseInterface
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * DatabaseInterface constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * starts a transaction.
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * commits a transaction.
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * executes a rollback of a open transaction.
     *
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * returns the errorCode if any.
     *
     * @return mixed
     */
    public function errorCode()
    {
        return $this->pdo->errorCode();
    }

    /**
     * returns an array of error information.
     *
     * @return array
     */
    public function errorInfo(): array
    {
        return $this->pdo->errorInfo();
    }

    /**
     * executes the provided statement with optional bindings.
     *
     * @param $statement
     * @param array $bindings
     * @return int
     */
    public function exec($statement, array $bindings = []): int
    {
        if ( empty($bindings) ) {
            return $this->pdo->exec($statement);
        }

        return $this->query($statement, $bindings)->rowCount();
    }

    /**
     * Database Driver Attributes getter.
     *
     * @param int $attribute
     * @return mixed
     */
    public function getAttribute(int $attribute)
    {
        return $this->pdo->getAttribute($attribute);
    }

    /**
     * Database Driver Attributes setter.
     *
     * @param int $attribute
     * @param $value
     * @return bool
     */
    public function setAttribute(int $attribute, $value): bool
    {
        $this->pdo->setAttribute($attribute, $value);
    }

    /**
     * returns an array of available drivers.
     *
     * @return array
     */
    public static function getAvailableDrivers(): array
    {
        return \PDO::getAvailableDrivers();
    }

    /**
     * checks whether an transaction is in progress or not.
     *
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    /**
     * returns the last inserted id by an optionally provided sequence.
     *
     * @param string|null $name
     * @return string
     */
    public function lastInsertId(string $name = null): string
    {
        return $this->pdo->lastInsertId($name);
    }

    /**
     * creates a prepared statement for the provided statement with applied optional driver options.
     *
     * @param $statement
     * @param array $driverOptions
     * @return DatabaseStatementInterface
     */
    public function prepare($statement, array $driverOptions = []): DatabaseStatementInterface
    {
        return new Statement($this->pdo->prepare($statement, $driverOptions));
    }

    /**
     * emits a query to the database with the optionally provided bindings.
     *
     * @param string $statement
     * @param array $bindings
     * @return DatabaseStatementInterface
     */
    public function query(string $statement, array $bindings = []): DatabaseStatementInterface
    {
        if ( empty($bindings) ) {
            return new Statement($this->pdo->query($statement));
        }

        $stmt = new Statement($this->pdo->prepare($statement));
        $stmt->execute($bindings);

        return $stmt;
    }

    /**
     * quotes the string by the provided parameter type (defaults to string)
     *
     * @param string $string
     * @param int $parameterType
     * @return string
     */
    public function quote(string $string, int $parameterType = \PDO::PARAM_STR): string
    {
        return $this->pdo->quote($string, $parameterType);
    }

    /**
     * quotes the string by the provided parameter type name (defaults to string and is compatible to gettype()).
     *
     * @param mixed $inbound
     * @param string $typename
     * @return string
     */
    public function quoteSimple($inbound, string $typename = 'auto'): string
    {
        $typename = strtolower($typename);

        if ( $typename === 'auto' ) {
            $typename = gettype($inbound);
        }

        switch($typename) {
            case 'integer':
            case 'int':
                return $this->quote((string) $inbound, \PDO::PARAM_INT);
                break;
            case 'boolean':
            case 'bool':
                return $this->quote((string) $inbound, \PDO::PARAM_BOOL);
                break;
            case 'null':
                return $this->quote('NULL', \PDO::PARAM_NULL);
                break;
            default:
                return $this->quote((string) $inbound, \PDO::PARAM_STR);
        }
    }
}