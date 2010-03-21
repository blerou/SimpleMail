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

interface SimpleMail_Sender
{
    /**
     * sendable email types
     */
    const TYPE_PLAIN = 'plain';
    const TYPE_HTML = 'html';
    const TYPE_BOTH = 'both';

    /**
     * send email
     */
    public function send();

    /**
     * template renderer getter
     *
     * @return SimpleMail_Template_Renderer
     */
    public function getRenderer();

    /**
     * template renderer setter
     *
     * @param  SimpleMail_Template_Renderer $template
     * @return SimpleMail_Sender
     */
    public function setRenderer(SimpleMail_Template_Renderer $template);

    /**
     * email type getter
     *
     * @return string
     */
    public function getType();

    /**
     * email type setter
     *
     * @param  string $type
     * @return SimpleMail_Sender
     */
    public function setType($type);

    /**
     * email attribute getter
     *
     * @param mixed $attr
     */
    public function getAttribute($attr);

    /**
     * email attributes setter
     *
     * @param  array $attrs
     * @return SimpleMail_Sender
     */
    public function setAttributes(array $attrs);
}