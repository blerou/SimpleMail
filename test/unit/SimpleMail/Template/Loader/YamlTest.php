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

require 'symfony/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

require_once dirname(__FILE__).'/../../../bootstrap.php';
require_once TEST_BASE_DIR.'/../src/SimpleMail/Template/Loader/Interface.php';
require_once TEST_BASE_DIR.'/../src/SimpleMail/Template/Loader/Yaml.php';

$template_file = TEST_BASE_DIR.'/fixtures/templates.yml';
$template_dir = TEST_BASE_DIR.'/fixtures/templates';


$t = new lime_test(11);

try {
  $loader = new SimpleMail_Template_Loader_Yaml(TEST_BASE_DIR.'/fixtures/no-templates.yml');
  
  $t->fail('->__construct() have to throw exception on invalid template file');
} catch (Exception $e) {
  $t->pass('->__construct() throw exception on invalid template file');
}


$t->diag('Fetching templates from one file');


$loader = new SimpleMail_Template_Loader_Yaml($template_file);

try {
  $foo = $loader->fetch('foo_template');
  
  $t->pass('->fetch() load valid template properly from template file');
  $t->is_deeply($foo['subject'], 'foo', '->fetch() load the correct template');
  $t->is($loader->modifiedAt('foo_template'), filemtime($template_file), '->modifiedAt() returns correct timestamp');
  
  $foo = $loader->fetch('foo_template');
  
  $t->is_deeply($foo['subject'], 'foo', '->fetch() second time load the correct template from cache');
} catch (Exception $e) {
  $t->fail('->fetch() have to load valid template properly from template file');
  $t->fail('->fetch() have to load the correct template');
  $t->fail('->modifiedAt() have to return correct timestamp');
  $t->fail('->fetch() have to load second time the correct template from cache');
}

try {
  $foo = $loader->fetch('invalid_template');
  
  $t->fail('->fetch() have to throw exception on invalid template');
} catch (Exception $e) {
  $t->pass('->fetch() throw exception on invalid template');
}


$t->diag('Fetching templates from one directory');


$loader = new SimpleMail_Template_Loader_Yaml(TEST_BASE_DIR.'/fixtures/templates');

try {
  $foo = $loader->fetch('foo_template');
  
  $t->pass('->fetch() load valid template properly from template file');
  $t->is_deeply($foo['subject'], 'foo', '->fetch() load the correct template');
  $t->is($loader->modifiedAt('foo_template'), filemtime($template_dir.'/foo_template.yml'), '->modifiedAt() returns correct timestamp');
} catch (Exception $e) {
  $t->fail('->fetch() have to load valid template properly from template file');
  $t->fail('->fetch() have to load the correct template');
  $t->fail('->modifiedAt() have to return correct timestamp');
}

try {
  $foo = $loader->fetch('invalid_template');
  
  $t->fail('->fetch() have to throw exception on invalid template');
} catch (Exception $e) {
  $t->pass('->fetch() throw exception on invalid template');
}

try {
  $loader->modifiedAt('invalid_template');
  
  $t->fail('->modifiedAt() have to throw exception on invalid template');
} catch (Exception $e) {
  $t->pass('->modifiedAt() throw exception on invalid template');
}
