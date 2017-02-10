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


trait ProviderTrait
{
    private $options = [];

    /**
     * ProviderTrait constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

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
     * @return mixed|null returns null when the queried option is not given.
     */
    protected function options(string $query)
    {
        $evaluate = '["'.str_replace('.', '"]["', $query).'"]';

        $available = eval('return isset($this->options'.$evaluate.');');

        if ( ! $available ) {
            return null;
        }

        return eval('return $this->options'.$evaluate.';');
    }
}