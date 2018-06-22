<?php
declare(strict_types=1);

namespace Kix\Apiranha\Examples\Definition;

use Kix\Apiranha\Annotation as Rest;

interface NonAnnotatedGithubApi
{
    /**
     * @Rest\CGet("/users/{username}/repos")
     * @Rest\Returns("\Kix\Apiranha\Examples\Model\Repository")
     */
    public function listRepos(string $username);

    /**
     * @Rest\Get("/repos/{username}/{repo}")
     * @Rest\Returns("\Kix\Apiranha\Examples\Model\Repository")
     */
    public function getRepo(string $username, string $repo);
}