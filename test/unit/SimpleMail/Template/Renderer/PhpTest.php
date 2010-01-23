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

require_once dirname(__FILE__).'/../../../bootstrap.php';

require 'symfony/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

require_once TEST_BASE_DIR.'/../src/SimpleMail/Template/Loader/Interface.php';
require_once TEST_BASE_DIR.'/../src/SimpleMail/Template/Loader/Yaml.php';
require_once TEST_BASE_DIR.'/../src/SimpleMail/Template/Renderer/Interface.php';
require_once TEST_BASE_DIR.'/../src/SimpleMail/Template/Renderer/Abstract.php';
require_once TEST_BASE_DIR.'/../src/SimpleMail/Template/Renderer/Php.php';

$loader = new SimpleMail_Template_Loader_Yaml(TEST_BASE_DIR.'/fixtures/templates');
$renderer = new SimpleMail_Template_Renderer_Php($loader);

$t = new lime_test(3);


$renderer
  ->setName('renderer')
  ->setVariables(array(
    'subject' => 'foo',
    'recipient_name' => 'bar',
    'body' => 'Lorem ipsum',
  ));

$subject = $renderer->getSubject();
$plain = $renderer->getPlain();
$html = $renderer->getHtml();

$plain_expected = "Plain bar!\n\nBody: Lorem ipsum";
$html_expected = "<h1>Html bar!</h1>\n<p>Body:<br>Lorem ipsum</p>\n";

$t->is($subject, 'subject foo', '->getSubject() works properly');
$t->is($plain, $plain_expected, '->getPlain() works properly');
$t->is($html, $html_expected, '->getHtml() works properly');