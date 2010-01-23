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

abstract class SimpleMail_Sender_Abstract
    implements SimpleMail_Sender_Interface
{
    /**
     * Email type (self::TYPE_* contstants)
     *
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $attrs = array();

    /**
     * @var SimpleMail_Template_Renderer_Interface
     */
    protected $template;

    /**
     * Construct method
     *
     * @param SimpleMail_Template_Renderer_Interface $template
     * @param string  $type   email type
     * @param array   $attrs  email attrs
     */
    public function __construct(SimpleMail_Template_Renderer_Interface $template, $type = self::TYPE_BOTH, array $attrs = array())
    {
        $this
            ->setRenderer($template)
            ->setType($type)
            ->setAttributes($attrs);
    }

    /**
     * template renderer getter
     *
     * @return SimpleMail_Template_Renderer_Interface
     */
    public function getRenderer()
    {
        return $this->template;
    }

    /**
     * template renderer setter
     *
     * @param  SimpleMail_Template_Renderer_Interface $template
     * @return SimpleMail_Sender_Interface
     */
    public function setRenderer(SimpleMail_Template_Renderer_Interface $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * email type getter
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * email type setter
     *
     * @param  string $type
     * @return SimpleMail_Sender_Interface
     */
    public function setType($type)
    {
        $valid_types = array(self::TYPE_BOTH, self::TYPE_HTML, self::TYPE_PLAIN);

        if (!in_array($type, $valid_types)) {
            throw new InvalidArgumentException("Invalid type: ".$type);
        }

        $this->type = $type;

        return $this;
    }

    /**
     * email attribute getter
     *
     * @param  string $attr
     * @return mixed
     */
    public function getAttribute($attr)
    {
        return isset($this->attrs[$attr])
            ? $this->attr[$attr]
            : null;
    }

    /**
     * email attributes setter
     *
     * @param  array $attrs
     * @return SimpleMail_Sender_Interface
     */
    public function setAttributes(array $attrs)
    {
        $this->attrs = array_merge($this->attrs, $attrs);

        return $this;
    }
}