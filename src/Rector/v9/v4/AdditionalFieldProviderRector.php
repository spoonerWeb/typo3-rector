<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\Rector\v9\v4;

use PhpParser\Node;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://docs.typo3.org/c/typo3/cms-core/master/en-us/Changelog/9.4/Deprecation-84387-DeprecatedMethodAndPropertyInSchedulerModuleController.html
 * @see \Ssch\TYPO3Rector\Tests\Rector\v9\v4\AdditionalFieldProviderRector\AdditionalFieldProviderRectorTest
 */
final class AdditionalFieldProviderRector extends AbstractRector
{
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class, MethodCall::class, PropertyFetch::class];
    }

    /**
     * @param Class_|PropertyFetch|MethodCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Class_) {
            return $this->refactorClass($node);
        }

        if ($node instanceof PropertyFetch) {
            return $this->refactorPropertyFetch($node);
        }

        return $this->refactorMethodCall($node);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Refactor AdditionalFieldProvider classes', [
            new CodeSample(
                <<<'CODE_SAMPLE'
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkip(Class_ $node): bool
    {
        foreach ($node->implements as $implement) {
            if ($this->isName($implement, 'TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface')) {
                return false;
            }
        }

        return true;
    }

    private function refactorClass(Class_ $node): ?Node
    {
        if ($this->shouldSkip($node)) {
            return null;
        }

        $node->extends = new FullyQualified('TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider');

        $implements = [];
        foreach ($node->implements as $implement) {
            if (! $this->isName($implement, 'TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface')) {
                $implements[] = $implement;
            }
        }
        $node->implements = $implements;

        return $node;
    }

    private function refactorPropertyFetch(PropertyFetch $node): ?Node
    {
        if (! $this->isObjectType(
            $node->var,
            new ObjectType('TYPO3\CMS\Scheduler\Controller\SchedulerModuleController')
        )) {
            return null;
        }

        if (! $this->isName($node->name, 'CMD')) {
            return null;
        }

        $methodCall = $this->nodeFactory->createMethodCall($node->var, 'getCurrentAction');

        return new String_($methodCall);
    }

    private function refactorMethodCall(MethodCall $node): ?Node
    {
        if (! $this->nodeTypeResolver->isMethodStaticCallOrClassMethodObjectType(
            $node,
            new ObjectType('TYPO3\CMS\Scheduler\Controller\SchedulerModuleController')
        )) {
            return null;
        }

        if (! $this->isName($node->name, 'addMessage')) {
            return null;
        }

        return $this->nodeFactory->createMethodCall('this', 'addMessage', $node->args);
    }
}
