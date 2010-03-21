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
 * Yaml based template loader
 *
 * Based on symfony's YAML component.
 * @link http://components.symfony-project.org/yaml/
 */
class SimpleMail_Template_YamlLoader implements SimpleMail_Template_Loader
{
    /**
     * @var string
     */
    protected $yamlFile = null;

    /**
     * @var array
     */
    protected $data = array();

    public function __construct($yaml_file)
    {
        $this->setYamlFile($yaml_file);
    }

    /**
     * setter of yaml file or directory of files
     *
     * @param  string $yaml_file
     * @return SimpleMail_Template_YamlLoader
     * @throws InvalidArgumentException
     */
    public function setYamlFile($yaml_file)
    {
        if (!file_exists($yaml_file)) {
            throw new InvalidArgumentException("Invalid yaml template file: $yaml_file");
        }
        $this->yamlFile = rtrim($yaml_file, '/');

        return $this;
    }

    /**
     * template getter
     *
     * @param  string $name
     * @return array
     * @throws InvalidArgumentException
     */
    public function fetch($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        if (is_dir($this->yamlFile)) {
            $yaml_file = $this->yamlFile."/$name.yml";

            if (!file_exists($yaml_file)) {
                throw new InvalidArgumentException("Invalid template file: $yaml_file");
            }

            $this->data[$name] = sfYaml::load($yaml_file);
        } else {
            $this->data = sfYaml::load($this->yamlFile);

            if (!isset($this->data[$name])) {
                throw new InvalidArgumentException("Invalid template name: $name");
            }
        }

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
        if (is_dir($this->yamlFile)) {
            $yaml_file = $this->yamlFile."/$name.yml";

            if (!file_exists($yaml_file)) {
              throw new InvalidArgumentException("Invalid file for last modification: $name");
            }
        } else {
            $yaml_file = $this->yamlFile;
        }

        return filemtime($yaml_file);
    }
}