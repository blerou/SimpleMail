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

class SimpleMail_Template_Renderer_Php
    extends SimpleMail_Template_Renderer_Abstract
{
    /**
     * default options:
     *  - tag_start
     *  - tag_end
     *
     * @var array
     */
    protected $options = array(
        'tag_start' => '{{',
        'tag_end' => '}}',
    );

    protected function render($field)
    {
        extract($this->variables);

        $_data = $this->loader->fetch($this->name);
        $_field = strtr($_data[$field], array(
            $this->getOption('tag_start') => '<?php echo ',
            $this->getOption('tag_end') => '?>',
        ));

        ob_start();
        eval('; ?>'.$_field.'<?php ;');
        return ob_get_clean();
    }

    /**
     * subject getter
     *
     * @see SimpleMail_Template_Renderer_Interface
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->render('subject');
    }

    /**
     * plain body getter
     *
     * @see SimpleMail_Template_Renderer_Interface
     *
     * @return string
     */
    public function getPlain()
    {
        return $this->render('plain');
    }

    /**
     * html body getter
     *
     * @see SimpleMail_Template_Renderer_Interface
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->render('html');
    }
}