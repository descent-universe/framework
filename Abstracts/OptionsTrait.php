<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Abstracts;


trait OptionsTrait
{
    /**
     * @var mixed[]
     */
    protected $options = [];

    /**
     * options getter.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return $this->options;
    }

    /**
     * options query command.
     *
     * @param string $query
     * @param string $value
     * @return mixed|null returns null when the queried option is not given.
     */
    protected function options(string $query, $value = null)
    {
        $evaluate = '["'.str_replace('.', '"]["', $query).'"]';

        if ( count(func_get_args()) === 2 ) {
            eval('$this->options'.$evaluate.' = $value;');

            return null;
        }

        return eval('return $this->options'.$evaluate.' ?? null;');
    }
}