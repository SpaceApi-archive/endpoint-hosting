<?php

namespace Application\Token;


use Application\Common\Collections\DirectoryArrayCollection;
use Application\Utils\Utils;
use Doctrine\Common\Collections\Criteria;

/**
 * Map with all the hackerspace tokens.
 * @package Application\Token
 */
class TokenList extends DirectoryArrayCollection
{
    /**
     * Reloads all the token after a new one got created.
     * @return bool success flag, false if the list wasn't reloaded
     */
    public function reload() {
        parent::reset();
        foreach ($this->_files as $file) {
            // don't consider the git ignore file
            if (basename($file) !== '.gitignore') {
                $this->add(new Token($file));
            }
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

    /**
     * Returns the slug or null for a given token.
     *
     * @param string $slug
     * @return Token|null token instance or null if there none
     */
    public function getTokenFromSlug($slug)
    {
        if ($this->count() === 0) {
            return null;
        }

        $criteria = Criteria::create()->where(
            Criteria::expr()->eq('slug', $slug)
        );

        $found = $this->matching($criteria);

        if($found->count() > 0) {
            return $found->first();
        }

        return null;
    }
} 