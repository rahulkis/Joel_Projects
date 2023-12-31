<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc6a401f85c366cbf3f9a8164210e3d5f
{
    public static $files = array (
        '18ff8532c766cb935c3659f187534cfa' => __DIR__ . '/../..' . '/lib/hoa/consistency/Prelude.php',
        '2ed3a196123003200f7571509eae20f8' => __DIR__ . '/../..' . '/lib/hoa/protocol/Wrapper.php',
    );

    public static $prefixLengthsPsr4 = array (
        'H' => 
        array (
            'Hoa\\Zformat\\' => 12,
            'Hoa\\Visitor\\' => 12,
            'Hoa\\Ustring\\' => 12,
            'Hoa\\Stream\\' => 11,
            'Hoa\\Regex\\' => 10,
            'Hoa\\Protocol\\' => 13,
            'Hoa\\Math\\' => 9,
            'Hoa\\Iterator\\' => 13,
            'Hoa\\File\\' => 9,
            'Hoa\\Exception\\' => 14,
            'Hoa\\Event\\' => 10,
            'Hoa\\Consistency\\' => 16,
            'Hoa\\Compiler\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Hoa\\Zformat\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hoa/zformat',
        ),
        'Hoa\\Visitor\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hoa/visitor',
        ),
        'Hoa\\Ustring\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hoa/ustring',
        ),
        'Hoa\\Stream\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hoa/stream',
        ),
        'Hoa\\Regex\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hoa/regex',
        ),
        'Hoa\\Protocol\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hoa/protocol',
        ),
        'Hoa\\Math\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hoa/math',
        ),
        'Hoa\\Iterator\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hoa/iterator',
        ),
        'Hoa\\File\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hoa/file',
        ),
        'Hoa\\Exception\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hoa/exception',
        ),
        'Hoa\\Event\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hoa/event',
        ),
        'Hoa\\Consistency\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hoa/consistency',
        ),
        'Hoa\\Compiler\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hoa/compiler',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Hoa\\Compiler\\Bin\\Pp' => __DIR__ . '/../..' . '/lib/hoa/compiler/Bin/Pp.php',
        'Hoa\\Compiler\\Exception\\Exception' => __DIR__ . '/../..' . '/lib/hoa/compiler/Exception/Exception.php',
        'Hoa\\Compiler\\Exception\\FinalStateHasNotBeenReached' => __DIR__ . '/../..' . '/lib/hoa/compiler/Exception/FinalStateHasNotBeenReached.php',
        'Hoa\\Compiler\\Exception\\IllegalToken' => __DIR__ . '/../..' . '/lib/hoa/compiler/Exception/IllegalToken.php',
        'Hoa\\Compiler\\Exception\\Lexer' => __DIR__ . '/../..' . '/lib/hoa/compiler/Exception/Lexer.php',
        'Hoa\\Compiler\\Exception\\Rule' => __DIR__ . '/../..' . '/lib/hoa/compiler/Exception/Rule.php',
        'Hoa\\Compiler\\Exception\\UnexpectedToken' => __DIR__ . '/../..' . '/lib/hoa/compiler/Exception/UnexpectedToken.php',
        'Hoa\\Compiler\\Exception\\UnrecognizedToken' => __DIR__ . '/../..' . '/lib/hoa/compiler/Exception/UnrecognizedToken.php',
        'Hoa\\Compiler\\Ll1' => __DIR__ . '/../..' . '/lib/hoa/compiler/Ll1.php',
        'Hoa\\Compiler\\Llk\\Lexer' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Lexer.php',
        'Hoa\\Compiler\\Llk\\Llk' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Llk.php',
        'Hoa\\Compiler\\Llk\\Parser' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Parser.php',
        'Hoa\\Compiler\\Llk\\Rule\\Analyzer' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Rule/Analyzer.php',
        'Hoa\\Compiler\\Llk\\Rule\\Choice' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Rule/Choice.php',
        'Hoa\\Compiler\\Llk\\Rule\\Concatenation' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Rule/Concatenation.php',
        'Hoa\\Compiler\\Llk\\Rule\\Ekzit' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Rule/Ekzit.php',
        'Hoa\\Compiler\\Llk\\Rule\\Entry' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Rule/Entry.php',
        'Hoa\\Compiler\\Llk\\Rule\\Invocation' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Rule/Invocation.php',
        'Hoa\\Compiler\\Llk\\Rule\\Repetition' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Rule/Repetition.php',
        'Hoa\\Compiler\\Llk\\Rule\\Rule' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Rule/Rule.php',
        'Hoa\\Compiler\\Llk\\Rule\\Token' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Rule/Token.php',
        'Hoa\\Compiler\\Llk\\Sampler\\BoundedExhaustive' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Sampler/BoundedExhaustive.php',
        'Hoa\\Compiler\\Llk\\Sampler\\Coverage' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Sampler/Coverage.php',
        'Hoa\\Compiler\\Llk\\Sampler\\Exception' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Sampler/Exception.php',
        'Hoa\\Compiler\\Llk\\Sampler\\Sampler' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Sampler/Sampler.php',
        'Hoa\\Compiler\\Llk\\Sampler\\Uniform' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/Sampler/Uniform.php',
        'Hoa\\Compiler\\Llk\\TreeNode' => __DIR__ . '/../..' . '/lib/hoa/compiler/Llk/TreeNode.php',
        'Hoa\\Compiler\\Test\\Integration\\Documentation' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Integration/Documentation.php',
        'Hoa\\Compiler\\Test\\Integration\\Llk\\Documentation' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Integration/Llk/Documentation.php',
        'Hoa\\Compiler\\Test\\Integration\\Llk\\Rule\\Analyzer' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Integration/Llk/Rule/Analyzer.php',
        'Hoa\\Compiler\\Test\\Integration\\Llk\\Soundness' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Integration/Llk/Soundness.php',
        'Hoa\\Compiler\\Test\\Unit\\Exception\\Exception' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Exception/Exception.php',
        'Hoa\\Compiler\\Test\\Unit\\Exception\\FinalStateHasNotBeenReached' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Exception/FinalStateHasNotBeenReached.php',
        'Hoa\\Compiler\\Test\\Unit\\Exception\\IllegalToken' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Exception/IllegalToken.php',
        'Hoa\\Compiler\\Test\\Unit\\Exception\\Lexer' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Exception/Lexer.php',
        'Hoa\\Compiler\\Test\\Unit\\Exception\\Rule' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Exception/Rule.php',
        'Hoa\\Compiler\\Test\\Unit\\Exception\\UnexpectedToken' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Exception/UnexpectedToken.php',
        'Hoa\\Compiler\\Test\\Unit\\Exception\\UnrecognizedToken' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Exception/UnrecognizedToken.php',
        'Hoa\\Compiler\\Test\\Unit\\Llk\\Lexer' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Llk/Lexer.php',
        'Hoa\\Compiler\\Test\\Unit\\Llk\\Llk' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Llk/Llk.php',
        'Hoa\\Compiler\\Test\\Unit\\Llk\\Rule\\Choice' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Llk/Rule/Choice.php',
        'Hoa\\Compiler\\Test\\Unit\\Llk\\Rule\\Concatenation' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Llk/Rule/Concatenation.php',
        'Hoa\\Compiler\\Test\\Unit\\Llk\\Rule\\Ekzit' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Llk/Rule/Ekzit.php',
        'Hoa\\Compiler\\Test\\Unit\\Llk\\Rule\\Entry' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Llk/Rule/Entry.php',
        'Hoa\\Compiler\\Test\\Unit\\Llk\\Rule\\Invocation' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Llk/Rule/Invocation.php',
        'Hoa\\Compiler\\Test\\Unit\\Llk\\Rule\\Repetition' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Llk/Rule/Repetition.php',
        'Hoa\\Compiler\\Test\\Unit\\Llk\\Rule\\Rule' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Llk/Rule/Rule.php',
        'Hoa\\Compiler\\Test\\Unit\\Llk\\Rule\\Token' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Llk/Rule/Token.php',
        'Hoa\\Compiler\\Test\\Unit\\Llk\\Sampler\\Exception' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Llk/Sampler/Exception.php',
        'Hoa\\Compiler\\Test\\Unit\\Llk\\TreeNode' => __DIR__ . '/../..' . '/lib/hoa/compiler/Test/Unit/Llk/TreeNode.php',
        'Hoa\\Compiler\\Visitor\\Dump' => __DIR__ . '/../..' . '/lib/hoa/compiler/Visitor/Dump.php',
        'Hoa\\Consistency\\Autoloader' => __DIR__ . '/../..' . '/lib/hoa/consistency/Autoloader.php',
        'Hoa\\Consistency\\Consistency' => __DIR__ . '/../..' . '/lib/hoa/consistency/Consistency.php',
        'Hoa\\Consistency\\Exception' => __DIR__ . '/../..' . '/lib/hoa/consistency/Exception.php',
        'Hoa\\Consistency\\Test\\Unit\\Autoloader' => __DIR__ . '/../..' . '/lib/hoa/consistency/Test/Unit/Autoloader.php',
        'Hoa\\Consistency\\Test\\Unit\\Consistency' => __DIR__ . '/../..' . '/lib/hoa/consistency/Test/Unit/Consistency.php',
        'Hoa\\Consistency\\Test\\Unit\\Exception' => __DIR__ . '/../..' . '/lib/hoa/consistency/Test/Unit/Exception.php',
        'Hoa\\Consistency\\Test\\Unit\\Xcallable' => __DIR__ . '/../..' . '/lib/hoa/consistency/Test/Unit/Xcallable.php',
        'Hoa\\Consistency\\Xcallable' => __DIR__ . '/../..' . '/lib/hoa/consistency/Xcallable.php',
        'Hoa\\Event\\Bucket' => __DIR__ . '/../..' . '/lib/hoa/event/Bucket.php',
        'Hoa\\Event\\Event' => __DIR__ . '/../..' . '/lib/hoa/event/Event.php',
        'Hoa\\Event\\Exception' => __DIR__ . '/../..' . '/lib/hoa/event/Exception.php',
        'Hoa\\Event\\Listenable' => __DIR__ . '/../..' . '/lib/hoa/event/Listenable.php',
        'Hoa\\Event\\Listener' => __DIR__ . '/../..' . '/lib/hoa/event/Listener.php',
        'Hoa\\Event\\Listens' => __DIR__ . '/../..' . '/lib/hoa/event/Listens.php',
        'Hoa\\Event\\Source' => __DIR__ . '/../..' . '/lib/hoa/event/Source.php',
        'Hoa\\Event\\Test\\Unit\\Bucket' => __DIR__ . '/../..' . '/lib/hoa/event/Test/Unit/Bucket.php',
        'Hoa\\Event\\Test\\Unit\\Event' => __DIR__ . '/../..' . '/lib/hoa/event/Test/Unit/Event.php',
        'Hoa\\Event\\Test\\Unit\\Exception' => __DIR__ . '/../..' . '/lib/hoa/event/Test/Unit/Exception.php',
        'Hoa\\Event\\Test\\Unit\\Listenable' => __DIR__ . '/../..' . '/lib/hoa/event/Test/Unit/Listenable.php',
        'Hoa\\Event\\Test\\Unit\\Listener' => __DIR__ . '/../..' . '/lib/hoa/event/Test/Unit/Listener.php',
        'Hoa\\Event\\Test\\Unit\\Listens' => __DIR__ . '/../..' . '/lib/hoa/event/Test/Unit/Listens.php',
        'Hoa\\Event\\Test\\Unit\\Source' => __DIR__ . '/../..' . '/lib/hoa/event/Test/Unit/Source.php',
        'Hoa\\Exception\\Error' => __DIR__ . '/../..' . '/lib/hoa/exception/Error.php',
        'Hoa\\Exception\\Exception' => __DIR__ . '/../..' . '/lib/hoa/exception/Exception.php',
        'Hoa\\Exception\\Group' => __DIR__ . '/../..' . '/lib/hoa/exception/Group.php',
        'Hoa\\Exception\\Idle' => __DIR__ . '/../..' . '/lib/hoa/exception/Idle.php',
        'Hoa\\Exception\\Test\\Unit\\Error' => __DIR__ . '/../..' . '/lib/hoa/exception/Test/Unit/Error.php',
        'Hoa\\Exception\\Test\\Unit\\Exception' => __DIR__ . '/../..' . '/lib/hoa/exception/Test/Unit/Exception.php',
        'Hoa\\Exception\\Test\\Unit\\Group' => __DIR__ . '/../..' . '/lib/hoa/exception/Test/Unit/Group.php',
        'Hoa\\Exception\\Test\\Unit\\Idle' => __DIR__ . '/../..' . '/lib/hoa/exception/Test/Unit/Idle.php',
        'Hoa\\File\\Directory' => __DIR__ . '/../..' . '/lib/hoa/file/Directory.php',
        'Hoa\\File\\Exception\\Exception' => __DIR__ . '/../..' . '/lib/hoa/file/Exception/Exception.php',
        'Hoa\\File\\Exception\\FileDoesNotExist' => __DIR__ . '/../..' . '/lib/hoa/file/Exception/FileDoesNotExist.php',
        'Hoa\\File\\File' => __DIR__ . '/../..' . '/lib/hoa/file/File.php',
        'Hoa\\File\\Finder' => __DIR__ . '/../..' . '/lib/hoa/file/Finder.php',
        'Hoa\\File\\Generic' => __DIR__ . '/../..' . '/lib/hoa/file/Generic.php',
        'Hoa\\File\\Link\\Link' => __DIR__ . '/../..' . '/lib/hoa/file/Link/Link.php',
        'Hoa\\File\\Link\\Read' => __DIR__ . '/../..' . '/lib/hoa/file/Link/Read.php',
        'Hoa\\File\\Link\\ReadWrite' => __DIR__ . '/../..' . '/lib/hoa/file/Link/ReadWrite.php',
        'Hoa\\File\\Link\\Write' => __DIR__ . '/../..' . '/lib/hoa/file/Link/Write.php',
        'Hoa\\File\\Read' => __DIR__ . '/../..' . '/lib/hoa/file/Read.php',
        'Hoa\\File\\ReadWrite' => __DIR__ . '/../..' . '/lib/hoa/file/ReadWrite.php',
        'Hoa\\File\\SplFileInfo' => __DIR__ . '/../..' . '/lib/hoa/file/SplFileInfo.php',
        'Hoa\\File\\Temporary\\Read' => __DIR__ . '/../..' . '/lib/hoa/file/Temporary/Read.php',
        'Hoa\\File\\Temporary\\ReadWrite' => __DIR__ . '/../..' . '/lib/hoa/file/Temporary/ReadWrite.php',
        'Hoa\\File\\Temporary\\Temporary' => __DIR__ . '/../..' . '/lib/hoa/file/Temporary/Temporary.php',
        'Hoa\\File\\Temporary\\Write' => __DIR__ . '/../..' . '/lib/hoa/file/Temporary/Write.php',
        'Hoa\\File\\Watcher' => __DIR__ . '/../..' . '/lib/hoa/file/Watcher.php',
        'Hoa\\File\\Write' => __DIR__ . '/../..' . '/lib/hoa/file/Write.php',
        'Hoa\\Iterator\\Aggregate' => __DIR__ . '/../..' . '/lib/hoa/iterator/Aggregate.php',
        'Hoa\\Iterator\\Append' => __DIR__ . '/../..' . '/lib/hoa/iterator/Append.php',
        'Hoa\\Iterator\\Buffer' => __DIR__ . '/../..' . '/lib/hoa/iterator/Buffer.php',
        'Hoa\\Iterator\\CallbackFilter' => __DIR__ . '/../..' . '/lib/hoa/iterator/CallbackFilter.php',
        'Hoa\\Iterator\\CallbackGenerator' => __DIR__ . '/../..' . '/lib/hoa/iterator/CallbackGenerator.php',
        'Hoa\\Iterator\\Counter' => __DIR__ . '/../..' . '/lib/hoa/iterator/Counter.php',
        'Hoa\\Iterator\\Demultiplexer' => __DIR__ . '/../..' . '/lib/hoa/iterator/Demultiplexer.php',
        'Hoa\\Iterator\\Directory' => __DIR__ . '/../..' . '/lib/hoa/iterator/Directory.php',
        'Hoa\\Iterator\\Exception' => __DIR__ . '/../..' . '/lib/hoa/iterator/Exception.php',
        'Hoa\\Iterator\\FileSystem' => __DIR__ . '/../..' . '/lib/hoa/iterator/FileSystem.php',
        'Hoa\\Iterator\\Filter' => __DIR__ . '/../..' . '/lib/hoa/iterator/Filter.php',
        'Hoa\\Iterator\\Glob' => __DIR__ . '/../..' . '/lib/hoa/iterator/Glob.php',
        'Hoa\\Iterator\\Infinite' => __DIR__ . '/../..' . '/lib/hoa/iterator/Infinite.php',
        'Hoa\\Iterator\\Iterator' => __DIR__ . '/../..' . '/lib/hoa/iterator/Iterator.php',
        'Hoa\\Iterator\\IteratorIterator' => __DIR__ . '/../..' . '/lib/hoa/iterator/IteratorIterator.php',
        'Hoa\\Iterator\\Limit' => __DIR__ . '/../..' . '/lib/hoa/iterator/Limit.php',
        'Hoa\\Iterator\\Lookahead' => __DIR__ . '/../..' . '/lib/hoa/iterator/Lookahead.php',
        'Hoa\\Iterator\\Lookbehind' => __DIR__ . '/../..' . '/lib/hoa/iterator/Lookbehind.php',
        'Hoa\\Iterator\\Map' => __DIR__ . '/../..' . '/lib/hoa/iterator/Map.php',
        'Hoa\\Iterator\\Mock' => __DIR__ . '/../..' . '/lib/hoa/iterator/Mock.php',
        'Hoa\\Iterator\\Multiple' => __DIR__ . '/../..' . '/lib/hoa/iterator/Multiple.php',
        'Hoa\\Iterator\\NoRewind' => __DIR__ . '/../..' . '/lib/hoa/iterator/NoRewind.php',
        'Hoa\\Iterator\\Outer' => __DIR__ . '/../..' . '/lib/hoa/iterator/Outer.php',
        'Hoa\\Iterator\\Recursive\\CallbackFilter' => __DIR__ . '/../..' . '/lib/hoa/iterator/Recursive/CallbackFilter.php',
        'Hoa\\Iterator\\Recursive\\Directory' => __DIR__ . '/../..' . '/lib/hoa/iterator/Recursive/Directory.php',
        'Hoa\\Iterator\\Recursive\\Filter' => __DIR__ . '/../..' . '/lib/hoa/iterator/Recursive/Filter.php',
        'Hoa\\Iterator\\Recursive\\Iterator' => __DIR__ . '/../..' . '/lib/hoa/iterator/Recursive/Iterator.php',
        'Hoa\\Iterator\\Recursive\\Map' => __DIR__ . '/../..' . '/lib/hoa/iterator/Recursive/Map.php',
        'Hoa\\Iterator\\Recursive\\Mock' => __DIR__ . '/../..' . '/lib/hoa/iterator/Recursive/Mock.php',
        'Hoa\\Iterator\\Recursive\\Recursive' => __DIR__ . '/../..' . '/lib/hoa/iterator/Recursive/Recursive.php',
        'Hoa\\Iterator\\Recursive\\RegularExpression' => __DIR__ . '/../..' . '/lib/hoa/iterator/Recursive/RegularExpression.php',
        'Hoa\\Iterator\\RegularExpression' => __DIR__ . '/../..' . '/lib/hoa/iterator/RegularExpression.php',
        'Hoa\\Iterator\\Repeater' => __DIR__ . '/../..' . '/lib/hoa/iterator/Repeater.php',
        'Hoa\\Iterator\\Seekable' => __DIR__ . '/../..' . '/lib/hoa/iterator/Seekable.php',
        'Hoa\\Iterator\\SplFileInfo' => __DIR__ . '/../..' . '/lib/hoa/iterator/SplFileInfo.php',
        'Hoa\\Iterator\\Test\\Unit\\Append' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Append.php',
        'Hoa\\Iterator\\Test\\Unit\\Buffer' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Buffer.php',
        'Hoa\\Iterator\\Test\\Unit\\CallbackFilter' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/CallbackFilter.php',
        'Hoa\\Iterator\\Test\\Unit\\CallbackGenerator' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/CallbackGenerator.php',
        'Hoa\\Iterator\\Test\\Unit\\Counter' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Counter.php',
        'Hoa\\Iterator\\Test\\Unit\\Demultiplexer' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Demultiplexer.php',
        'Hoa\\Iterator\\Test\\Unit\\Directory' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Directory.php',
        'Hoa\\Iterator\\Test\\Unit\\FileSystem' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/FileSystem.php',
        'Hoa\\Iterator\\Test\\Unit\\Filter' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Filter.php',
        'Hoa\\Iterator\\Test\\Unit\\Infinite' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Infinite.php',
        'Hoa\\Iterator\\Test\\Unit\\IteratorIterator' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/IteratorIterator.php',
        'Hoa\\Iterator\\Test\\Unit\\Limit' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Limit.php',
        'Hoa\\Iterator\\Test\\Unit\\Lookahead' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Lookahead.php',
        'Hoa\\Iterator\\Test\\Unit\\Lookbehind' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Lookbehind.php',
        'Hoa\\Iterator\\Test\\Unit\\Map' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Map.php',
        'Hoa\\Iterator\\Test\\Unit\\Mock' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Mock.php',
        'Hoa\\Iterator\\Test\\Unit\\Multiple' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Multiple.php',
        'Hoa\\Iterator\\Test\\Unit\\NoRewind' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/NoRewind.php',
        'Hoa\\Iterator\\Test\\Unit\\RegularExpression' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/RegularExpression.php',
        'Hoa\\Iterator\\Test\\Unit\\Repeater' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/Repeater.php',
        'Hoa\\Iterator\\Test\\Unit\\SplFileInfo' => __DIR__ . '/../..' . '/lib/hoa/iterator/Test/Unit/SplFileInfo.php',
        'Hoa\\Math\\Bin\\Calc' => __DIR__ . '/../..' . '/lib/hoa/math/Bin/Calc.php',
        'Hoa\\Math\\Combinatorics\\Combination\\CartesianProduct' => __DIR__ . '/../..' . '/lib/hoa/math/Combinatorics/Combination/CartesianProduct.php',
        'Hoa\\Math\\Combinatorics\\Combination\\Combination' => __DIR__ . '/../..' . '/lib/hoa/math/Combinatorics/Combination/Combination.php',
        'Hoa\\Math\\Combinatorics\\Combination\\Gamma' => __DIR__ . '/../..' . '/lib/hoa/math/Combinatorics/Combination/Gamma.php',
        'Hoa\\Math\\Context' => __DIR__ . '/../..' . '/lib/hoa/math/Context.php',
        'Hoa\\Math\\Exception\\AlreadyDefinedConstant' => __DIR__ . '/../..' . '/lib/hoa/math/Exception/AlreadyDefinedConstant.php',
        'Hoa\\Math\\Exception\\Exception' => __DIR__ . '/../..' . '/lib/hoa/math/Exception/Exception.php',
        'Hoa\\Math\\Exception\\UnknownConstant' => __DIR__ . '/../..' . '/lib/hoa/math/Exception/UnknownConstant.php',
        'Hoa\\Math\\Exception\\UnknownFunction' => __DIR__ . '/../..' . '/lib/hoa/math/Exception/UnknownFunction.php',
        'Hoa\\Math\\Exception\\UnknownVariable' => __DIR__ . '/../..' . '/lib/hoa/math/Exception/UnknownVariable.php',
        'Hoa\\Math\\Sampler\\Random' => __DIR__ . '/../..' . '/lib/hoa/math/Sampler/Random.php',
        'Hoa\\Math\\Sampler\\Sampler' => __DIR__ . '/../..' . '/lib/hoa/math/Sampler/Sampler.php',
        'Hoa\\Math\\Test\\Unit\\Combinatorics\\Combination\\CartesianProduct' => __DIR__ . '/../..' . '/lib/hoa/math/Test/Unit/Combinatorics/Combination/CartesianProduct.php',
        'Hoa\\Math\\Test\\Unit\\Combinatorics\\Combination\\Combination' => __DIR__ . '/../..' . '/lib/hoa/math/Test/Unit/Combinatorics/Combination/Combination.php',
        'Hoa\\Math\\Test\\Unit\\Combinatorics\\Combination\\Gamma' => __DIR__ . '/../..' . '/lib/hoa/math/Test/Unit/Combinatorics/Combination/Gamma.php',
        'Hoa\\Math\\Test\\Unit\\Context' => __DIR__ . '/../..' . '/lib/hoa/math/Test/Unit/Context.php',
        'Hoa\\Math\\Test\\Unit\\Issue' => __DIR__ . '/../..' . '/lib/hoa/math/Test/Unit/Issue.php',
        'Hoa\\Math\\Test\\Unit\\Sampler\\Random' => __DIR__ . '/../..' . '/lib/hoa/math/Test/Unit/Sampler/Random.php',
        'Hoa\\Math\\Test\\Unit\\Visitor\\Arithmetic' => __DIR__ . '/../..' . '/lib/hoa/math/Test/Unit/Visitor/Arithmetic.php',
        'Hoa\\Math\\Util' => __DIR__ . '/../..' . '/lib/hoa/math/Util.php',
        'Hoa\\Math\\Visitor\\Arithmetic' => __DIR__ . '/../..' . '/lib/hoa/math/Visitor/Arithmetic.php',
        'Hoa\\Protocol\\Bin\\Resolve' => __DIR__ . '/../..' . '/lib/hoa/protocol/Bin/Resolve.php',
        'Hoa\\Protocol\\Exception' => __DIR__ . '/../..' . '/lib/hoa/protocol/Exception.php',
        'Hoa\\Protocol\\Node\\Library' => __DIR__ . '/../..' . '/lib/hoa/protocol/Node/Library.php',
        'Hoa\\Protocol\\Node\\Node' => __DIR__ . '/../..' . '/lib/hoa/protocol/Node/Node.php',
        'Hoa\\Protocol\\Protocol' => __DIR__ . '/../..' . '/lib/hoa/protocol/Protocol.php',
        'Hoa\\Protocol\\Test\\Unit\\Exception' => __DIR__ . '/../..' . '/lib/hoa/protocol/Test/Unit/Exception.php',
        'Hoa\\Protocol\\Test\\Unit\\Node\\Library' => __DIR__ . '/../..' . '/lib/hoa/protocol/Test/Unit/Node/Library.php',
        'Hoa\\Protocol\\Test\\Unit\\Node\\Node' => __DIR__ . '/../..' . '/lib/hoa/protocol/Test/Unit/Node/Node.php',
        'Hoa\\Protocol\\Test\\Unit\\Protocol' => __DIR__ . '/../..' . '/lib/hoa/protocol/Test/Unit/Protocol.php',
        'Hoa\\Protocol\\Test\\Unit\\Wrapper' => __DIR__ . '/../..' . '/lib/hoa/protocol/Test/Unit/Wrapper.php',
        'Hoa\\Protocol\\Wrapper' => __DIR__ . '/../..' . '/lib/hoa/protocol/Wrapper.php',
        'Hoa\\Regex\\Exception' => __DIR__ . '/../..' . '/lib/hoa/regex/Exception.php',
        'Hoa\\Regex\\Visitor\\Isotropic' => __DIR__ . '/../..' . '/lib/hoa/regex/Visitor/Isotropic.php',
        'Hoa\\Stream\\Bucket' => __DIR__ . '/../..' . '/lib/hoa/stream/Bucket.php',
        'Hoa\\Stream\\Composite' => __DIR__ . '/../..' . '/lib/hoa/stream/Composite.php',
        'Hoa\\Stream\\Context' => __DIR__ . '/../..' . '/lib/hoa/stream/Context.php',
        'Hoa\\Stream\\Exception' => __DIR__ . '/../..' . '/lib/hoa/stream/Exception.php',
        'Hoa\\Stream\\Filter\\Basic' => __DIR__ . '/../..' . '/lib/hoa/stream/Filter/Basic.php',
        'Hoa\\Stream\\Filter\\Exception' => __DIR__ . '/../..' . '/lib/hoa/stream/Filter/Exception.php',
        'Hoa\\Stream\\Filter\\Filter' => __DIR__ . '/../..' . '/lib/hoa/stream/Filter/Filter.php',
        'Hoa\\Stream\\Filter\\LateComputed' => __DIR__ . '/../..' . '/lib/hoa/stream/Filter/LateComputed.php',
        'Hoa\\Stream\\IStream\\Bufferable' => __DIR__ . '/../..' . '/lib/hoa/stream/IStream/Bufferable.php',
        'Hoa\\Stream\\IStream\\In' => __DIR__ . '/../..' . '/lib/hoa/stream/IStream/In.php',
        'Hoa\\Stream\\IStream\\Lockable' => __DIR__ . '/../..' . '/lib/hoa/stream/IStream/Lockable.php',
        'Hoa\\Stream\\IStream\\Out' => __DIR__ . '/../..' . '/lib/hoa/stream/IStream/Out.php',
        'Hoa\\Stream\\IStream\\Pathable' => __DIR__ . '/../..' . '/lib/hoa/stream/IStream/Pathable.php',
        'Hoa\\Stream\\IStream\\Pointable' => __DIR__ . '/../..' . '/lib/hoa/stream/IStream/Pointable.php',
        'Hoa\\Stream\\IStream\\Statable' => __DIR__ . '/../..' . '/lib/hoa/stream/IStream/Statable.php',
        'Hoa\\Stream\\IStream\\Stream' => __DIR__ . '/../..' . '/lib/hoa/stream/IStream/Stream.php',
        'Hoa\\Stream\\IStream\\Structural' => __DIR__ . '/../..' . '/lib/hoa/stream/IStream/Structural.php',
        'Hoa\\Stream\\IStream\\Touchable' => __DIR__ . '/../..' . '/lib/hoa/stream/IStream/Touchable.php',
        'Hoa\\Stream\\Stream' => __DIR__ . '/../..' . '/lib/hoa/stream/Stream.php',
        'Hoa\\Stream\\Test\\Integration\\Filter\\Filter' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Integration/Filter/Filter.php',
        'Hoa\\Stream\\Test\\Integration\\Filter\\LateComputed' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Integration/Filter/LateComputed.php',
        'Hoa\\Stream\\Test\\Integration\\Stream' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Integration/Stream.php',
        'Hoa\\Stream\\Test\\Unit\\Bucket' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/Bucket.php',
        'Hoa\\Stream\\Test\\Unit\\Composite' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/Composite.php',
        'Hoa\\Stream\\Test\\Unit\\Context' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/Context.php',
        'Hoa\\Stream\\Test\\Unit\\Exception' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/Exception.php',
        'Hoa\\Stream\\Test\\Unit\\Filter\\Basic' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/Filter/Basic.php',
        'Hoa\\Stream\\Test\\Unit\\Filter\\Exception' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/Filter/Exception.php',
        'Hoa\\Stream\\Test\\Unit\\Filter\\Filter' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/Filter/Filter.php',
        'Hoa\\Stream\\Test\\Unit\\IStream\\Bufferable' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/IStream/Bufferable.php',
        'Hoa\\Stream\\Test\\Unit\\IStream\\In' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/IStream/In.php',
        'Hoa\\Stream\\Test\\Unit\\IStream\\Lockable' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/IStream/Lockable.php',
        'Hoa\\Stream\\Test\\Unit\\IStream\\Out' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/IStream/Out.php',
        'Hoa\\Stream\\Test\\Unit\\IStream\\Pathable' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/IStream/Pathable.php',
        'Hoa\\Stream\\Test\\Unit\\IStream\\Pointable' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/IStream/Pointable.php',
        'Hoa\\Stream\\Test\\Unit\\IStream\\Statable' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/IStream/Statable.php',
        'Hoa\\Stream\\Test\\Unit\\IStream\\Stream' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/IStream/Stream.php',
        'Hoa\\Stream\\Test\\Unit\\IStream\\Structural' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/IStream/Structural.php',
        'Hoa\\Stream\\Test\\Unit\\IStream\\Touchable' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/IStream/Touchable.php',
        'Hoa\\Stream\\Test\\Unit\\Stream' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/Stream.php',
        'Hoa\\Stream\\Test\\Unit\\Wrapper\\Exception' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/Wrapper/Exception.php',
        'Hoa\\Stream\\Test\\Unit\\Wrapper\\IWrapper\\File' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/Wrapper/IWrapper/File.php',
        'Hoa\\Stream\\Test\\Unit\\Wrapper\\IWrapper\\IWrapper' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/Wrapper/IWrapper/IWrapper.php',
        'Hoa\\Stream\\Test\\Unit\\Wrapper\\IWrapper\\Stream' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/Wrapper/IWrapper/Stream.php',
        'Hoa\\Stream\\Test\\Unit\\Wrapper\\Wrapper' => __DIR__ . '/../..' . '/lib/hoa/stream/Test/Unit/Wrapper/Wrapper.php',
        'Hoa\\Stream\\Wrapper\\Exception' => __DIR__ . '/../..' . '/lib/hoa/stream/Wrapper/Exception.php',
        'Hoa\\Stream\\Wrapper\\IWrapper\\File' => __DIR__ . '/../..' . '/lib/hoa/stream/Wrapper/IWrapper/File.php',
        'Hoa\\Stream\\Wrapper\\IWrapper\\IWrapper' => __DIR__ . '/../..' . '/lib/hoa/stream/Wrapper/IWrapper/IWrapper.php',
        'Hoa\\Stream\\Wrapper\\IWrapper\\Stream' => __DIR__ . '/../..' . '/lib/hoa/stream/Wrapper/IWrapper/Stream.php',
        'Hoa\\Stream\\Wrapper\\Wrapper' => __DIR__ . '/../..' . '/lib/hoa/stream/Wrapper/Wrapper.php',
        'Hoa\\Ustring\\Bin\\Fromcode' => __DIR__ . '/../..' . '/lib/hoa/ustring/Bin/Fromcode.php',
        'Hoa\\Ustring\\Bin\\Tocode' => __DIR__ . '/../..' . '/lib/hoa/ustring/Bin/Tocode.php',
        'Hoa\\Ustring\\Exception' => __DIR__ . '/../..' . '/lib/hoa/ustring/Exception.php',
        'Hoa\\Ustring\\Search' => __DIR__ . '/../..' . '/lib/hoa/ustring/Search.php',
        'Hoa\\Ustring\\Test\\Unit\\Issue' => __DIR__ . '/../..' . '/lib/hoa/ustring/Test/Unit/Issue.php',
        'Hoa\\Ustring\\Test\\Unit\\Search' => __DIR__ . '/../..' . '/lib/hoa/ustring/Test/Unit/Search.php',
        'Hoa\\Ustring\\Test\\Unit\\Ustring' => __DIR__ . '/../..' . '/lib/hoa/ustring/Test/Unit/Ustring.php',
        'Hoa\\Ustring\\Ustring' => __DIR__ . '/../..' . '/lib/hoa/ustring/Ustring.php',
        'Hoa\\Visitor\\Element' => __DIR__ . '/../..' . '/lib/hoa/visitor/Element.php',
        'Hoa\\Visitor\\Test\\Unit\\Element' => __DIR__ . '/../..' . '/lib/hoa/visitor/Test/Unit/Element.php',
        'Hoa\\Visitor\\Test\\Unit\\Visit' => __DIR__ . '/../..' . '/lib/hoa/visitor/Test/Unit/Visit.php',
        'Hoa\\Visitor\\Visit' => __DIR__ . '/../..' . '/lib/hoa/visitor/Visit.php',
        'Hoa\\Zformat\\Exception' => __DIR__ . '/../..' . '/lib/hoa/zformat/Exception.php',
        'Hoa\\Zformat\\Parameter' => __DIR__ . '/../..' . '/lib/hoa/zformat/Parameter.php',
        'Hoa\\Zformat\\Parameterizable' => __DIR__ . '/../..' . '/lib/hoa/zformat/Parameterizable.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc6a401f85c366cbf3f9a8164210e3d5f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc6a401f85c366cbf3f9a8164210e3d5f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc6a401f85c366cbf3f9a8164210e3d5f::$classMap;

        }, null, ClassLoader::class);
    }
}
