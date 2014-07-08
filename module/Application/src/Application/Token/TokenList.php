<?php

namespace Application\Token;


use Application\Utils\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * Map with all the hackerspace tokens.
 * @package Application\Token
 */
class TokenList extends ArrayCollection
{
    /**
     * @var string Directory containing all the token files
     */
    protected $tokenDir;

    /**
     * Creates a new TokenList instance in two ways:
     *
     * <ul>
     *   <li>(default) empty array of elements passed, token directory provided</li>
     *   <li>array of elements provided while a given token directory not considered</li>
     * </ul>
     *
     * @param array $elements list of tokens which this instance should be initialized from
     * @param string $tokenDir directory that contains the token files
     */
    function __construct($elements = array(), $tokenDir = '') {
        if (! empty($elements)) {
            parent::__construct($elements);
        } else {
            $this->tokenDir = $tokenDir;
            $this->reload();
        }
    }

    /**
     * Reloads all the token after a new one got created.
     * @return bool success flag, false if the list wasn't reloaded
     */
    public function reload() {

        $token_files = Utils::getFilesFromDir($this->tokenDir);

        $this->clear();
        foreach ($token_files as $file) {
            $this->add(new Token($file));
        }
    }

    /**
     * Returns the slug or null for a given token.
     *
     * @param string $token
     * @return string|null Normalized space name or null if there's no match
     */
    public function getSlugFromToken($token)
    {
        if ($this->count() === 0) {
            return null;
        }

        $criteria = Criteria::create()->where(
            Criteria::expr()->eq('token', $token)
        );

        $found = $this->matching($criteria);

        if($found->count() > 0) {
            return $found->first()->getSlug();
        }

        return null;
    }
} 