<?php

namespace Viktorprogger\YiisoftInform\SubDomain\GitHub\Infrastructure\Client;

use Github\Api\Organization as GithubOrganization;
use RuntimeException;

class Organization extends GithubOrganization
{
    /**
     * @param string $organization
     * @param array  $params
     * @param int    $page
     *
     * @return array
     */
    public function events(string $organization, array $params = [], int $page = 1): array
    {
        $result = $this->get('/orgs/'.rawurlencode($organization).'/events', array_merge(['page' => $page], $params));
        if (!is_array($result)) {
            throw new RuntimeException($result);
        }

        return $result;
    }
}
