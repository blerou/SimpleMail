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

require_once dirname(__FILE__).'/../../bootstrap.php';
require_once TEST_BASE_DIR.'/../src/SimpleMail/Template/Loader.php';
require_once TEST_BASE_DIR.'/../src/SimpleMail/Template/PdoLoader.php';


$pdo = new PDO('sqlite::memory');
$pdo->exec('drop table `email_templates`');
$pdo->exec('create table `email_templates` (
    `template` varchar(100),
    `subject` varchar(100),
    `modified_at` timestamp
)');
$pdo->exec('drop table `test_templates`');
$pdo->exec('create table `test_templates` (
    `name` varchar(100),
    `subj` varchar(100),
    `mod` timestamp
)');

$pdo->exec('insert into email_templates values ("foo", "bar", '.strtotime('2010-01-01').')');
$pdo->exec('insert into test_templates values ("bar", "foo", '.strtotime('2010-01-01').')');


$t = new lime_test(9);


$t->diag('Fetching template with default settings');


try {
    $loader = new SimpleMail_Template_PdoLoader($pdo);
    $data = $loader->fetch('foo');

    $t->pass('->fetch() load valid template properly from template database');
    $t->is_deeply($data['subject'], 'bar', '->fetch() load the correct template');
    $t->is($loader->modifiedAt('foo'), strtotime('2010-01-01'), '->modifiedAt() returns correct timestamp');
  
    $data = $loader->fetch('foo');
  
    $t->is_deeply($data['subject'], 'bar', '->fetch() second time load the correct template from cache');
} catch (Exception $e) {
    $t->fail('->fetch() have to load valid template properly from template database');
    $t->fail('->fetch() have to load the correct template');
    $t->fail('->modifiedAt() have to return correct timestamp');
    $t->fail('->fetch() have to load second time the correct template from cache');
}


$t->diag('Fetching invalid template');


try {
    $loader->fetch('bar');
    $t->fail('->fetch() have to throw exception on invalid template');
} catch (Exception $e) {
    $t->pass('->fetch() throw exception on invalid template');
}


$t->diag('Fetching templates with custom settings');


try {
    $loader = new SimpleMail_Template_PdoLoader($pdo, array(
        'table_name' => 'test_templates',
        'name_field' => 'name',
        'subject_field' => 'subj',
        'modified_at_field' => 'mod',
    ));
    $data = $loader->fetch('bar');

    $t->pass('->fetch() load valid template properly from template database');
    $t->is_deeply($data['subj'], 'foo', '->fetch() load the correct template');
    $t->is($loader->modifiedAt('bar'), strtotime('2010-01-01'), '->modifiedAt() returns correct timestamp');

    $data = $loader->fetch('bar');

    $t->is_deeply($data['subj'], 'foo', '->fetch() second time load the correct template from cache');
} catch (Exception $e) {
    $t->fail('->fetch() have to load valid template properly from template database');
    $t->fail('->fetch() have to load the correct template');
    $t->fail('->modifiedAt() have to return correct timestamp');
    $t->fail('->fetch() have to load second time the correct template from cache');
}