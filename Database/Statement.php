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


use Descent\Database\Contracts\DatabaseStatementInterface;
use Descent\Database\Exceptions\DatabaseException;

/**
 * Class Statement
 * @package Descent\Database
 */
class Statement implements DatabaseStatementInterface
{
    /**
     * @var \PDOStatement
     */
    protected $statement;

    /**
     * Statement constructor.
     *
     * @param \PDOStatement $statement
     */
    public function __construct(\PDOStatement $statement)
    {
        $this->statement = $statement;
    }

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
    public function bindColumn($column, &$param, $type = null, int $maxlen = null, $driverData = null): bool
    {
        if ( null === $type ) {
            return $this->statement->bindColumn($column, $param);
        }

        $type = static::toPDOType($type);

        if ( null === $maxlen ) {
            return $this->statement->bindColumn($column, $param, $type);
        }

        if ( null === $driverData ) {
            return $this->statement->bindColumn($column, $param, $type, $maxlen);
        }

        return $this->statement->bindColumn($column, $param, $type, $maxlen, $driverData);
    }

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
    public function bindParam($parameter, &$variable, $type = \PDO::PARAM_STR, int $length = null, $driverData = null): bool
    {
        $type = static::toPDOType($type);

        if ( null === $length ) {
            return $this->statement->bindParam($parameter, $variable, $type);
        }

        if ( null === $driverData ) {
            return $this->statement->bindParam($parameter, $variable, $type, $length);
        }

        return $this->statement->bindParam($parameter, $variable, $type, $length, $driverData);
    }

    /**
     * binds the provided value to the provided parameter.
     *
     * @param int|string $parameter number index for ?-placeholders or placeholder name for named placeholders
     * @param mixed $value the value to bind
     * @param string|int $type \PDO::PARAM_* integer representation or gettype()-orientated type name
     * @return bool
     */
    public function bindValue($parameter, $value, $type = \PDO::PARAM_STR): bool
    {
        $type = static::toPDOType($type);

        return $this->bindValue($parameter, $value, $type);
    }

    /**
     * closes the actual cursor.
     *
     * @return bool
     */
    public function closeCursor(): bool
    {
        return $this->statement->closeCursor();
    }

    /**
     * returns the column count of the executed statement.
     *
     * @return int
     */
    public function columnCount(): int
    {
        return $this->statement->columnCount();
    }

    /**
     * dumps (directly sends to the output stream) the debug values of the statement. Use getDebugDumpParams() to
     * fetch it into a variable.
     *
     * @return void
     */
    public function debugDumpParams()
    {
        $this->statement->debugDumpParams();
    }

    /**
     * returns a dump of the debug vales of the statement.
     *
     * @return string
     */
    public function getDebugDumpParams(): string
    {
        ob_start();
        $this->debugDumpParams();
        return ob_get_clean();
    }

    /**
     * returns the errorCode of the current statement.
     *
     * @return string
     */
    public function errorCode(): string
    {
        return $this->statement->errorCode();
    }

    /**
     * returns an array of error information of the current statement.
     *
     * @return array
     */
    public function errorInfo(): array
    {
        return $this->statement->errorInfo();
    }

    /**
     * executes the statement with optionally provided bindings.
     *
     * @param array $bindings
     * @return bool
     */
    public function execute(array $bindings = []): bool
    {
        return $this->statement->execute($bindings);
    }

    /**
     * fetches the next row from the result set of the executed statement.
     *
     * @param int|null $fetchStyle
     * @param int $cursorOrientation
     * @param int $cursorOffset
     * @return mixed
     */
    public function fetch(int $fetchStyle = null, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0)
    {
        return $this->statement->fetch($fetchStyle, $cursorOrientation, $cursorOffset);
    }

    /**
     * fetches the result set of the executed statement into an array.
     *
     * @param int|null $fetchStyle
     * @param null $fetchArgument
     * @param array $parameters
     * @return mixed[]
     */
    public function fetchAll(int $fetchStyle = null, $fetchArgument = null, array $parameters = []): array
    {
        return $this->statement->fetchAll($fetchStyle, $fetchArgument, $parameters);
    }

    /**
     * fetches a specific column from the current statement's next row.
     *
     * @param int $columnNumber
     * @return mixed
     */
    public function fetchColumn(int $columnNumber = 0)
    {
        return $this->statement->fetchColumn($columnNumber);
    }

    /**
     * fetches the next row from the result set of the executed statement into an object.
     *
     * @param string $className
     * @param array $parameters
     * @return object
     */
    public function fetchObject(string $className = 'stdClass', array $parameters = [])
    {
        return $this->statement->fetchObject($className, $parameters);
    }

    /**
     * attribute getter.
     *
     * @param int $attribute
     * @return mixed
     */
    public function getAttribute(int $attribute)
    {
        return $this->statement->getAttribute($attribute);
    }

    /**
     * attribute setter.
     *
     * @param int $attribute
     * @param $value
     * @return bool
     */
    public function setAttribute(int $attribute, $value): bool
    {
        return $this->statement->setAttribute($attribute, $value);
    }

    /**
     * fetches the meta data of the current statement for a specific column.
     *
     * @param int $column
     * @return array
     */
    public function getColumnMeta(int $column): array
    {
        return $this->statement->getColumnMeta($column);
    }

    /**
     * Advances to the next row of the current executed statement.
     *
     * @return bool
     */
    public function nextRowSet(): bool
    {
        return $this->statement->nextRowset();
    }

    /**
     * returns the affected row count. SELECT statements may not provided correct values
     * due to different (probably non-standard) database platform implementations.
     *
     * @return int
     */
    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    /**
     * setter for the fetch mode of the current statement.
     *
     * @param int $mode
     * @param array $options
     * @return bool
     */
    public function setFetchMode(int $mode, array $options = []): bool
    {
        if ( empty($options) ) {
            return $this->statement->setFetchMode($mode);
        }

        $options = array_change_key_case($options, CASE_LOWER);

        if (
            \PDO::FETCH_COLUMN === $mode &&
            (
                array_key_exists('colno', $options) ||
                array_key_exists('columnnumber', $options)
            )
        ) {
            $columnNumber = $options['colno'] ?? $options['columnnumber'];
            return $this->statement->setFetchMode(\PDO::FETCH_COLUMN, $columnNumber);
        }

        if (
            \PDO::FETCH_CLASS === $mode &&
            (
                array_key_exists('class', $options) ||
                array_key_exists('classname', $options) ||
                array_key_exists('interface', $options)
            )
        ) {
            $classname = $options['class'] ?? $options['classname'] ?? $options['interface'];
            return $this->statement->setFetchMode(\PDO::FETCH_CLASS, $classname, (array) $options['ctor'] ?? []);
        }

        if ( \PDO::FETCH_INTO === $mode && array_key_exists('object', $options) ) {
            return $this->statement->setFetchMode(\PDO::FETCH_INTO, $options['object']);
        }

        throw new DatabaseException('Unknown fetch mode or required options not given');
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Generator An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator(): \Generator
    {
        yield from $this->statement;
    }

    /**
     * maps the iterator of the current statement to the provided callback. Returns an array. The array is constructed
     * by the callbacks return value.
     *
     * @param callable $callback
     * @param bool|false $returnGenerator
     * @return array|\Generator
     */
    public function each(callable $callback, bool $returnGenerator = false)
    {
        $output = [];

        foreach ( $this->getIterator() as $key => $value ) {
            $row = call_user_func($callback, $value);

            if ( $returnGenerator ) {
                yield $key => $row;
                continue;
            }

            $output[$key] = $row;
        }

        if ( ! $returnGenerator ) {
            return $output;
        }
    }

    /**
     * encodes all rows to json. When an callback is provided this method will encode the result of each().
     *
     * @param callable|null $callback
     * @param int $options
     * @param int $depth
     * @return string
     */
    public function json(callable $callback = null, int $options, int $depth = 512): string
    {
        if ( ! is_callable($callback) ) {
            return json_encode($this->statement->fetchAll(\PDO::FETCH_ASSOC), $options, $depth);
        }

        return json_encode($this->each($callback, false), $options, $depth);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return $this->rowCount();
    }


    /**
     * transports a gettype()-compatible string or \PDO::PARAM_* integer representation into a \PDO::PARAM_*
     * integer representation of a type.
     *
     * @param $type
     * @return int
     */
    public static function toPDOType($type): int
    {
        if ( is_int($type) ) {
            return $type;
        }

        switch($type) {
            case 'int':
            case 'integer':
                $realType = \PDO::PARAM_INT;
                break;
            case 'res':
            case 'resource':
                $realType = \PDO::PARAM_LOB;
                break;
            case 'bool':
            case 'boolean':
                $realType = \PDO::PARAM_BOOL;
                break;
            case 'string':
            default:
                $realType = \PDO::PARAM_STR;
        }

        return $realType;
    }
}