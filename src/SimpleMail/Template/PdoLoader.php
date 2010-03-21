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

/**
 * PDO based template loader
 */
class SimpleMail_Template_PdoLoader implements SimpleMail_Template_Loader
{
    /**
     * @var string
     */
    protected $pdo = null;

    /**
     * template cache
     *
     * @var array
     */
    protected $data = array();

    /**
     * options
     *
     * @var array
     */
    protected $options = array(
        'table_name' => 'email_templates',
        'name_field' => 'template',
        'modified_at_field' => 'modified_at'
    );

    public function __construct(PDO $pdo, array $options = array())
    {
        $this->options = array_merge($this->options, $options);
        $this->setPDO($pdo);
    }

    /**
     * setter of PDO instance
     *
     * @param  PDO $pdo
     * @return SimpleMail_Template_PdoLoader
     */
    public function setPDO(PDO $pdo)
    {
        $this->pdo = $pdo;
        return $this;
    }

    /**
     * template getter
     *
     * @param  string $name
     * @return array
     * @throws PDOException
     * @throws InvalidArgumentException
     */
    public function fetch($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        $query = "SELECT * FROM %table% WHERE %name% = ?";
        $stmt = $this->pdo->prepare(strtr($query, array(
            '%table%' => $this->options['table_name'],
            '%name%'  => $this->options['name_field'],
        )));
        $stmt->execute(array($name));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (false === $result) {
            throw new InvalidArgumentException('Invalid template name: '.$name);
        }

        $this->data[$name] = $result;

        return $this->data[$name];
    }

    /**
     * getter of last modification timestamp of the given template
     *
     * @param  string $name
     * @return int
     * @throws InvalidArgumentException
     */
    public function modifiedAt($name)
    {
        $data = $this->fetch($name);

        return $data[$this->options['modified_at_field']];
    }
}