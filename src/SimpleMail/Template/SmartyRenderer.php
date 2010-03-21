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

class SimpleMail_Template_SmartyRenderer extends SimpleMail_Template_RendererImp
{
    /**
     * smarty instance
     *
     * @var Smarty
     */
    protected $smarty = null;

    /**
     * default options
     *
     * @var array
     */
    protected $options = array(
        'left_delimiter' => '{{',
        'right_delimiter' => '}}',
    );

    /**
     * Constructor
     *
     * Available options:
     *  - cache_dir: smarty complile dir
     *  - left_delimiter: default {{
     *  - right_delimiter: default }}
     *
     * @param  SimpleMail_Template_Loader  $loader
     * @param  Smarty  $smarty
     * @param  array   $options
     */
    public function __construct(SimpleMail_Template_Loader $loader, Smarty $smarty = null, array $options = array())
    {
        parent::__construct($loader, $options);

        if ($smarty) {
            $this->setSmarty($smarty);
        }
    }

    /**
     * template variable setter
     *
     * clear previous Smarty assignments
     *
     * @param  array $variables
     * @return SimpleMail_Template_SmartyRenderer
     */
    public function setVariables(array $variables)
    {
        $this->smarty->clear_all_assign();

        return parent::setVariables($variables);
    }

    /**
     * subject getter
     *
     * @see SimpleMail_Template_Renderer
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->smarty->fetch('subject:'.$this->name);
    }

    /**
     * plain body getter
     *
     * @see SimpleMail_Template_Renderer
     *
     * @return string
     */
    public function getPlain()
    {
        return $this->smarty->fetch('plain:'.$this->name);
    }

    /**
     * html body getter
     *
     * @see SimpleMail_Template_Renderer
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->smarty->fetch('html:'.$this->name);
    }

    /**
     * Smarty instance getter
     *
     * @return Smarty
     */
    public function getSmarty()
    {
        if (!$this->smarty) {
            $this->setSmarty(new Smarty());
        }
        return $this->smarty;
    }

    /**
     * Smarty instance setter
     *
     * it also setup Smarty with predefined values
     *
     * @param  Smarty $smarty
     * @return SimpleMail_Template_SmartyRenderer
     */
    public function setSmarty(Smarty $smarty)
    {
        $this->smarty = $smarty;
        $this->smarty->register_resource('plain', array(
            array($this, 'loadPlain'),
            array($this, 'loadTimestamp'),
            array($this, 'loadSecure'),
            array($this, 'loadTrusted'),
        ));
        $this->o_smarty->register_resource('html', array(
            array($this, 'loadHtml'),
            array($this, 'loadTimestamp'),
            array($this, 'loadSecure'),
            array($this, 'loadTrusted'),
        ));
        $this->smarty->register_resource('subject', array(
            array($this, 'loadSubject'),
            array($this, 'loadTimestamp'),
            array($this, 'loadSecure'),
            array($this, 'loadTrusted'),
        ));

        $cache_dir = $this->getOption('cache_dir');
        if ($cache_dir) {
            $this->smarty->compile_dir = $cache_dir;
        }
        $this->smarty->left_delimiter  = $this->getOption('left_delimiter');
        $this->smarty->right_delimiter = $this->getOption('right_delimiter');

        return $this;
    }

    // {{{ Smarty resource handlers

    public function loadPlain($tpl_name, &$tpl_source, &$smarty)
    {
        $data = $this->loader->fetch($tpl_name);

        if (!isset($data['plain'])) {
            return false;
        }

        $tpl_source = $data['plain'];

        return true;
    }


    public function loadHtml($tpl_name, &$tpl_source, &$smarty)
    {
        $data = $this->loader->fetch($tpl_name);

        if (!isset($data['html'])) {
            return false;
        }

        $tpl_source = $data['html'];

        return true;
    }

    public function loadSubject($tpl_name, &$tpl_source, &$smarty)
    {
        $data = $this->loader->fetch($tpl_name);

        if (!isset($data['subject'])) {
            return false;
        }

        $tpl_source = $data['subject'];

        return true;
    }

    public function loadTimestamp($tpl_name, &$tpl_timestamp, &$smarty)
    {
        $last_modification = $this->loader->modifiedAt($tpl_name);

        if (!$last_modification) {
            return false;
        }

        $tpl_timestamp = ctype_digit($last_modification)
            ? $last_modification
            : strtotime($last_modification);

        return true;
    }

    public function loadSecure($tpl_name, &$smarty)
    {
        // assume all templates are secure
        return true;
    }

    public function loadTrusted($tpl_name, &$smarty)
    {
        // not used for templates
    }

    // }}} Smarty resource handlers
}