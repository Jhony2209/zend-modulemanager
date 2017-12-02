<?php
/**
 * @link      http://github.com/zendframework/zend-modulemanager for the canonical source repository
 * @copyright Copyright (c) 2005-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-modulemanager/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

if (! class_exists(\PHPUnit_Framework_Assert::class)) {
    class_alias(\PHPUnit\Framework\Assert::class, \PHPUnit_Framework_Assert::class, true);
}