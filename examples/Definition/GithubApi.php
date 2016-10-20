<?php

namespace Kix\Apiranha\Examples\Definition;

use Kix\Apiranha\Annotation as Rest;

/**
 * Class GithubApi
 */
interface GithubApi
{
    /**
     * @Rest\CGet("/users/{username}/repos")
     * @Rest\Returns("\Kix\Apiranha\Examples\Model\Repository")
     * @param string $username
     * @return \Kix\Apiranha\Examples\Model\Repository[]
     */
    public function listRepos(string $username);

    /**
     * @Rest\Get("/repos/{username}/{repo}")
     * @Rest\Returns("\Kix\Apiranha\Examples\Model\Repository")
     * @param string $username
     * @param string $repo
     * @return \Kix\Apiranha\Examples\Model\Repository
     */
    public function getRepo(string $username, string $repo);
}
