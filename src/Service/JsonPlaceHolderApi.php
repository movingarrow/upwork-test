<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JsonPlaceHolderApi
{
    /**
     * @param HttpClientInterface $client
     */
    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function fetchUsersFromApi(): array
    {
        $response = $this->client->request(
            'GET',
            'https://jsonplaceholder.typicode.com/users'
        );

        if ($response->getStatusCode() !== 200) {
            throw new HttpException(400, "Error receiving users list from api");
        }

        return $response->toArray();
    }

    /**
     * @param string $username
     * @param string $name
     * @param string $email
     * @param string $phone
     * @param string $website
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function createUser(string $username, string $name, string $email, string $phone, string $website): array
    {
        $response = $this->client->request(
            'POST',
            'https://jsonplaceholder.typicode.com/users',
            [
                'headers' => [
                    'Content-Type' => 'application/json; charset=UTF-8',
                ],
                'body' => [
                    'username' => $username,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'website' => $website,
                ],
            ]
        );

        if ($response->getStatusCode() !== 201) {
            throw new HttpException(401, "Error posting user to api");
        }

        return $response->toArray();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function deleteUser(string $id): array
    {
        $response = $this->client->request(
            'DELETE',
            'https://jsonplaceholder.typicode.com/users/' . $id
        );

        if ($response->getStatusCode() !== 200) {
            throw new HttpException(400, "Error receiving users list from api");
        }

        return $response->toArray();
    }
}
