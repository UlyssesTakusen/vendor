<?php

declare(strict_types=1);

namespace loophp\phptree\Node;

use drupol\phpmerkle\Hasher\DoubleSha256;
use drupol\phpmerkle\Hasher\HasherInterface;
use loophp\phptree\Modifier\FulfillCapacity;
use loophp\phptree\Modifier\ModifierInterface;
use loophp\phptree\Modifier\RemoveNullNode;

/**
 * Class MerkleNode.
 */
class MerkleNode extends ValueNode implements MerkleNodeInterface
{
    /**
     * @var HasherInterface
     */
    private $hasher;

    /**
     * @var ModifierInterface[]
     */
    private $modifiers = [];

    /**
     * MerkleNode constructor.
     *
     * @param mixed $value
     * @param HasherInterface $hasher
     */
    public function __construct(
        $value,
        int $capacity = 2,
        ?HasherInterface $hasher = null
    ) {
        parent::__construct($value, $capacity, null, null);

        $this->hasher = $hasher ?? new DoubleSha256();
        $this->modifiers = [
            new RemoveNullNode(),
            new FulfillCapacity(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function hash(): string
    {
        return $this->hasher->unpack($this->doHash($this->normalize()));
    }

    /**
     * {@inheritdoc}
     */
    public function label(): string
    {
        return $this->isLeaf() ?
            $this->getValue() :
            $this->hash();
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(): MerkleNodeInterface
    {
        return array_reduce(
            $this->modifiers,
            static function (MerkleNodeInterface $tree, ModifierInterface $modifier): MerkleNodeInterface {
                return $modifier->modify($tree);
            },
            $this->clone()
        );
    }

    /**
     * {@inheritdoc}
     */
    private function doHash(MerkleNodeInterface $node): string
    {
        // If node is a leaf, then compute its hash from its value.
        if ($node->isLeaf()) {
            return $this->hasher->hash($node->getValue());
        }

        $hash = '';
        /** @var MerkleNodeInterface $child */
        foreach ($node->children() as $child) {
            $hash .= $this->doHash($child);
        }

        return $this->hasher->hash($hash);
    }
}
