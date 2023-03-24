<?php

namespace Foo;

use UseStmt;
use UseStmt\MoreNamespaces;

use Foo\Bar\Baz\OtherNamespace;

class ClassClass extends ExtendsClass
{
    use UseTrait;

    public function refStaticConst() { \StaticConst::Foo; }
    public function refStaticVal() { \StaticVal::$val; }
    public function refStaticCall() { \StaticCall::foo(); }
    public function refConstructor() { new \Constructor(); }

    public function refStaticConstWithUsedNamespace() { OtherNamespace\StaticConst::Foo; }
    public function refStaticValWithUsedNamespace() { OtherNamespace\StaticVal::$val; }
    public function refStaticCallWithUsedNamespace() { OtherNamespace\StaticCall::foo(); }
    public function refConstructorWithUsedNamespace() { new OtherNamespace\Constructor(); }


    public function refStaticConstWithFullNamespace() { \SomewhereNamespace\StaticConst::Foo; }
    public function refStaticValWithFullNamespace() { \SomewhereNamespace\StaticVal::$val; }
    public function refStaticCallWithFullNamespace() { \SomewhereNamespace\StaticCall::foo(); }
    public function refConstructorWithFullNamespace() { new \SomewhereNamespace\Constructor(); }
}
