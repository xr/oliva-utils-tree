<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\SimpleBuilder;

require_once __DIR__ . '/bootstrap.php';
require_once SCENES . '/DefaultScene.php';

use Tester\Assert;
use Oliva\Utils\Tree\Builder\SimpleTreeBuilder;
use Oliva\Utils\Tree\Node\SimpleNode;

// run the tests
subroutine1();
subroutine2();


function subroutine1()
{
	$data = ['a' => 'a', 'b' => 'b', 'children' => [
			['c' => 'c', 'children' => [['e' => 'e', 'f' => 'f',]]],
			['d' => 'd', 'children' => [['g' => 'g', 'h' => 'h',]]],
	]];

	$builder = new SimpleTreeBuilder('children');
	$root = $builder->build($data);

	Assert::same(['a' => 'a', 'b' => 'b'], $root->getContents());
	Assert::same(['c' => 'c'], $root->getChild(0)->getContents());
	Assert::same(['d' => 'd'], $root->getChild(1)->getContents());
	Assert::same(['e' => 'e', 'f' => 'f'], $root->getChild(0)->getChild(0)->getContents());
}


function subroutine2()
{
	$data = ['a' => 'a', NULL => ['c', 'd', ['e' => 'ee']]];
	$builder = new SimpleTreeBuilder('');
	$builder->nodeClass = SimpleNode::CLASS;

	// default build
	$root = $builder->build($data);
	Assert::type(SimpleNode::CLASS, $root);
	Assert::same('d', $root->getChild(1)->getContents());
	Assert::same(['e' => 'ee'], $root->getChild(2)->getContents());

	// custom build callback
	$builder->setNodeCallback(function($data, $class) {
		if (is_array($data) && count($data) === 1) {
			$data = reset($data);
		}
		return new $class($data);
	}, SimpleNode::CLASS);
	$root2 = $builder->build($data);
	Assert::type(SimpleNode::CLASS, $root2);
	Assert::same('d', $root2->getChild(1)->getContents());
	Assert::same('ee', $root2->getChild(2)->getContents());
}