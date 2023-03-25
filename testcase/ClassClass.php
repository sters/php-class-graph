<?php

namespace Foo;

use UseStmt;
use UseStmt\MoreNamespaces;
use function UseStmt\ImportFunction\Func;
use function UseStmt\ImportFunctionWithAlias\Func as func2;
use const UseStmt\ImportConst\ConstVal;
use UseStmt\AliasReference as AliasRef;
use Foo\Bar\Baz\AnotherNamespace;
use Foo\Bar\Baz\AnotherClass;
use MultipleUse\One, MultipleUse\Two, MultipleUse\ThreeAlias as three;

class ClassClass extends ExtendsClass
{
    use UseTrait;

    public function refSelf() { self::do(); }
    public function refParent() { parent::do(); }
    public function refStatic() { static::do(); }

    public function refStaticConst() { \StaticConst::Foo; }
    public function refStaticVal() { \StaticVal::$val; }
    public function refStaticCall() { \StaticCall::foo(); }
    public function refConstructor() { new \Constructor(); }

    public function refStaticConstWithUsedClass() { AnotherClass::Foo; }

    public function refStaticConstWithUsedNamespace() { AnotherNamespace\StaticConst::Foo; }
    public function refStaticValWithUsedNamespace() { AnotherNamespace\StaticVal::$val; }
    public function refStaticCallWithUsedNamespace() { AnotherNamespace\StaticCall::foo(); }
    public function refConstructorWithUsedNamespace() { new AnotherNamespace\Constructor(); }

    public function refStaticConstWithFullNamespace() { \SomewhereNamespace\StaticConst::Foo; }
    public function refStaticValWithFullNamespace() { \SomewhereNamespace\StaticVal::$val; }
    public function refStaticCallWithFullNamespace() { \SomewhereNamespace\StaticCall::foo(); }
    public function refConstructorWithFullNamespace() { new \SomewhereNamespace\Constructor(); }

    public function refStaticConstWithImported() { \Foo\Bar\Baz\AnotherClass::Foo; }
    public function refStaticValWithImported() { \Foo\Bar\Baz\AnotherClass::$val; }
    public function refStaticCallWithImported() { \Foo\Bar\Baz\AnotherClass::foo(); }
    public function refConstructorWithImported() { new \Foo\Bar\Baz\AnotherClass(); }

    public function refAliasUse() { AliasRef\Foo::foo(); }
}
