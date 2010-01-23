<?php

/*
 * This file is part of the SimpleMail library.
 *
 * Copyright (c) 2009-2010 Szabolcs Sulik <sulik.szabolcs@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

abstract class SimpleMail_Template_Renderer_Abstract
    implements SimpleMail_Template_Renderer_Interface
{
    /**
     * @var SimpleMail_Template_Loader_Interface
     */
    protected $loader = null;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var array
     */
    protected $variables = array();

    /**
     * @var array
     */
    protected $options = array();

    public function __construct(SimpleMail_Template_Loader_Interface $loader, array $options = array())
    {
        $this->loader = $loader;
        $this->setOptions($options);
    }

    /**
     * template name setter
     *
     * @param  string $name
     * @return SimpleMail_Template_Renderer_Abstract
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * template variable setter
     *
     * @param  array $variables
     * @return SimpleMail_Template_Renderer_Abstract
     */
    public function setVariables(array $variables)
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * option getter
     *
     * @param  string $name
     * @return mixed
     */
    protected function getOption($name)
    {
        return isset($this->options[$name]) 
            ? $this->options[$name]
            : null;
    }

    /**
     * options setter
     *
     * @param  array $options
     * @return void
     */
    protected function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }
}