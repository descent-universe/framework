<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Contracts;


use Descent\Database\Contracts\DatabaseStatementInterface;

interface DatabaseInterface
{
    /**
     * DatabaseInterface constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo);

    /**
     * starts a transaction.
     *
     * @return bool
     */
    public function beginTransaction(): bool;

    /**
     * commits a transaction.
     *
     * @return bool
     */
    public function commit(): bool;

    /**
     * executes a rollback of a open transaction.
     *
     * @return bool
     */
    public function rollBack(): bool;

    /**
     * returns the errorCode if any.
     *
     * @return mixed
     */
    public function errorCode();

    /**
     * returns an array of error information.
     *
     * @return array
     */
    public function errorInfo(): array;

    /**
     * executes the provided statement with optional bindings.
     *
     * @param $statement
     * @param array $bindings
     * @return int
     */
    public function exec($statement, array $bindings = []): int;

    /**
     * Database Driver Attributes getter.
     *
     * @param int $attribute
     * @return mixed
     */
    public function getAttribute(int $attribute);

    /**
     * Database Driver Attributes setter.
     *
     * @param int $attribute
     * @param $value
     * @return bool
     */
    public function setAttribute(int $attribute, $value): bool;

    /**
     * returns an array of available drivers.
     *
     * @return array
     */
    public static function getAvailableDrivers(): array;

    /**
     * checks whether an transaction is in progress or not.
     *
     * @return bool
     */
    public function inTransaction(): bool;

    /**
     * returns the last inserted id by an optionally provided sequence.
     *
     * @param string|null $name
     * @return string
     */
    public function lastInsertId(string $name = null): string;

    /**
     * creates a prepared statement for the provided statement with applied optional driver options.
     *
     * @param $statement
     * @param array $driverOptions
     * @return DatabaseStatementInterface
     */
    public function prepare($statement, array $driverOptions = []): DatabaseStatementInterface;

    /**
     * emits a query to the database with the optionally provided bindings.
     *
     * @param string $statement
     * @param array $bindings
     * @return DatabaseStatementInterface
     */
    public function query(string $statement, array $bindings = []): DatabaseStatementInterface;

    /**
     * quotes the string by the provided parameter type (defaults to string)
     *
     * @param string $string
     * @param int $parameterType
     * @return string
     */
    public function quote(string $string, int $parameterType = \PDO::PARAM_STR): string;

    /**
     * quotes the string by the provided parameter type name (defaults to string and is compatible to gettype()).
     *
     * @param mixed $inbound
     * @param string $typename
     * @return string
     */
    public function quoteSimple($inbound, string $typename = 'auto'): string;
}