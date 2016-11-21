<?php

namespace App;

class EventumApi
{
    public function __construct()
    {
    }

    public function getIssueDetails($issue_id)
    {
        $data = $this->getClient()->getIssueDetails($issue_id);

        // FIXME: fix this eventum side!
        $data['iss_last_action_date'] =
            $data['iss_last_internal_action_date'] > $data['iss_last_public_action_date'] ?
                $data['iss_last_internal_action_date'] : $data['iss_last_public_action_date'];

        // FIXME: this too
        $data['iss_issue_link'] = $this->createIssueLink($issue_id);

        return $data;
    }


    public function createIssueLink($issue_id)
    {
        // XXX: this should be improved eventum side to provide information
        return 'http://localhost/eventum/view.php?id=' . $issue_id;
    }

    /**
     * @var \RemoteApi|\Eventum_RPC
     */
    private $client;

    /**
     * @return \RemoteApi|\Eventum_RPC
     */
    protected function getClient()
    {
        if (!$this->client) {
            // TODO: use config!
            $url = 'http://localhost/eventum/rpc/xmlrpc.php';
            $this->client = new \Eventum_RPC($url);
            $auth = [
                'username' => '',
                'token' => '',
            ];

            $this->client->setCredentials($auth['username'], $auth['token']);
        }

        return $this->client;
    }
}