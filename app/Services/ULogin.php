<?php

namespace App\Services;
use GuzzleHttp\Client;
use Illuminate\Contracts\Logging\Log;
use \Validator;

/**
 * Created by PhpStorm.
 * User: Frexin
 * Date: 07.08.2016
 * Time: 0:02
 */
class ULogin {

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Log
     */
    private $logger;

    /**
     * ULogin constructor.
     * @param \GuzzleHttp\Client $client
     * @param \Illuminate\Contracts\Logging\Log
     */
    public function __construct(\GuzzleHttp\Client $client, Log $logger) {
        $this->client = $client;
        $this->logger = $logger;
        $this->logger->useFiles(storage_path().'/logs/ulogin.log', 'warning');
    }

    public function getUserDataByToken($token) {
        $data = [];

        try {
            $response = $this->client->post('https://frexin.auth0.com/tokeninfo', ['form_params' => ['id_token' => $token]]);
            $body = $response->getBody();
            $userData = json_decode($body, true);

            if (!$userData || empty($userData)) {
                throw new \Exception('Invalid JSON');
            }

            if (isset($userData['error'])) {
                throw new \Exception('Ulogin error: ' . $userData['error']);
            }

            if (!isset($userData['identities'][0])) {
                throw new \Exception('Ulogin validation error: network or uid not present');
            }

            $data = [
                'network' => $userData['identities'][0]['provider'],
                'uid' => $userData['identities'][0]['user_id'],
                'photo' => $userData['picture'],
                'first_name' => $userData['given_name'],
                'last_name' => $userData['family_name'],
            ];

            if (!$this->validate($data)) {
                throw new \Exception('Ulogin validation error: network or uid not present');
            }

        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
            $data['error'] = $e->getMessage();
        }

        return $data;
    }

    /**
     * @param $data
     * @return boolean
     */
    private function validate($data)
    {
        $validator = Validator::make($data, [
            'network' => 'required',
            'uid'     => 'required',
        ]);

        return $validator->passes();
    }
}