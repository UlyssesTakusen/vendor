<?php

declare(strict_types=1);

namespace loophp\phptree\Traverser;

use loophp\phptree\Node\NodeInterface;
use Traversable;

/**
 * Class PostOrder.
 */
class PostOrder implements TraverserInterface
{
    /**
     * @var int
     */
    private $index = 0;

    /**
     * {@inheritdoc}
     */
    public function traverse(NodeInterface $node): Traversable
    {
        $this->index = 0;

        return $this->doTraverse($node);
    }

    /**
     * @return Traversable<NodeInterface>
     */
    private function doTraverse(NodeInterface $node): Traversable
    {
        foreach ($node->children() as $child) {
            yield from $this->doTraverse($child);
            ++$this->index;
        }

        yield $this->index => $node;
    }
}
