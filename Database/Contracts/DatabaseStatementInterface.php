<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Database\Contracts;


interface DatabaseStatementInterface extends \Countable, \IteratorAggregate
{
    /**
     * binds the provided column named or 1-indexed to the provided parameter variable.
     *
     * @param int|string $column the column name or 1-indexed column index
     * @param mixed $param the variable to bind
     * @param string|int|null $type \PDO::PARAM_* integer representation or gettype()-orientated type name
     * @param int|null $maxlen the maximum length for allocation
     * @param mixed|null $driverData additional driver data
     * @return bool
     */
    public function bindColumn($column, &$param, $type = null, int $maxlen = null, $driverData = null): bool;

    /**
     * binds the provided field to the provided variable.
     *
     * @param int|string $parameter number index for ?-placeholders or placeholder name for named placeholders
     * @param mixed $variable the variable to bind
     * @param string|int $type \PDO::PARAM_* integer representation or gettype()-orientated type name
     * @param int|null $length the value length of the field
     * @param null $driverData additional driver data
     * @return bool
     */
    public function bindParam($parameter, &$variable, $type = \PDO::PARAM_STR, int $length = null, $driverData = null): bool;

    /**
     * binds the provided value to the provided parameter.
     *
     * @param int|string $parameter number index for ?-placeholders or placeholder name for named placeholders
     * @param mixed $value the value to bind
     * @param string|int $type \PDO::PARAM_* integer representation or gettype()-orientated type name
     * @return bool
     */
    public function bindValue($parameter, $value, $type = \PDO::PARAM_STR): bool;

    /**
     * closes the actual cursor.
     *
     * @return bool
     */
    public function closeCursor(): bool;

    /**
     * returns the column count of the executed statement.
     *
     * @return int
     */
    public function columnCount(): int;

    /**
     * dumps (directly sends to the output stream) the debug values of the statement. Use getDebugDumpParams() to
     * fetch it into a variable.
     *
     * @return void
     */
    public function debugDumpParams();

    /**
     * returns a dump of the debug vales of the statement.
     *
     * @return string
     */
    public function getDebugDumpParams(): string;

    /**
     * returns the errorCode of the current statement.
     *
     * @return string
     */
    public function errorCode(): string;

    /**
     * returns an array of error information of the current statement.
     *
     * @return array
     */
    public function errorInfo(): array;

    /**
     * executes the statement with optionally provided bindings.
     *
     * @param array $bindings
     * @return bool
     */
    public function execute(array $bindings = []): bool;

    /**
     * fetches the next row from the result set of the executed statement.
     *
     * @param int|null $fetchStyle
     * @param int $cursorOrientation
     * @param int $cursorOffset
     * @return mixed
     */
    public function fetch(int $fetchStyle = null, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0);

    /**
     * fetches the result set of the executed statement into an array.
     *
     * @param int|null $fetchStyle
     * @param null $fetchArgument
     * @param array $parameters for the constructor
     * @return mixed[]
     */
    public function fetchAll(int $fetchStyle = null, $fetchArgument = null, array $parameters = []): array;

    /**
     * fetches a specific column from the current statement's next row.
     *
     * @param int $columnNumber
     * @return mixed
     */
    public function fetchColumn(int $columnNumber = 0);

    /**
     * fetches the next row from the result set of the executed statement into an object.
     *
     * @param string $className
     * @param array $parameters
     * @return object
     */
    public function fetchObject(string $className = 'stdClass', array $parameters = []);

    /**
     * attribute getter.
     *
     * @param int $attribute
     * @return mixed
     */
    public function getAttribute(int $attribute);

    /**
     * attribute setter.
     *
     * @param int $attribute
     * @param $value
     * @return bool
     */
    public function setAttribute(int $attribute, $value): bool;

    /**
     * fetches the meta data of the current statement for a specific column.
     *
     * @param int $column
     * @return array
     */
    public function getColumnMeta(int $column): array;

    /**
     * Advances to the next row of the current executed statement.
     *
     * @return bool
     */
    public function nextRowSet(): bool;

    /**
     * returns the affected row count. SELECT statements may not provided correct values
     * due to different (probably non-standard) database platform implementations.
     *
     * @return int
     */
    public function rowCount(): int;

    /**
     * setter for the fetch mode of the current statement.
     *
     * @param int $mode
     * @param array $options
     * @return bool
     */
    public function setFetchMode(int $mode, array $options = []): bool;

    /**
     * maps the iterator of the current statement to the provided callback. Returns an array. The array is constructed
     * by the callbacks return value.
     *
     * @param callable $callback
     * @param bool|false $returnGenerator
     * @return array|\Generator
     */
    public function each(callable $callback, bool $returnGenerator = false);

    /**
     * encodes all rows to json. When an callback is provided this method will encode the result of each().
     *
     * @param callable|null $callback
     * @param int $options
     * @param int $depth
     * @return string
     */
    public function json(callable $callback = null, int $options, int $depth = 512): string;
}