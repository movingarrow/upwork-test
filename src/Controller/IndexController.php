<?php

namespace App\Controller;

use App\Service\JsonPlaceHolderApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class IndexController extends AbstractController
{
    /**
     * @var JsonPlaceHolderApi
     */
    private JsonPlaceHolderApi $service;

    /**
     * @param JsonPlaceHolderApi $service
     */
    public function __construct(JsonPlaceHolderApi $service)
    {
        $this->service = $service;
    }

    /**
     * @return Response
     */
    #[Route('/', name: 'list_users')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/create_user_form', name: 'create_user_form')]
    public function createUserForm(): Response
    {
        return $this->render('index/create_user_form.html.twig', [
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[Route('/create_user', name: 'create_user')]
    public function createUser(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('create-user', $request->request->get('token'))) {
            throw new HttpException(400, "Error, form is not valid");
        }

        $username = $request->request->get('username');
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $phone = $request->request->get('phone');
        $website = $request->request->get('website');

        if (!isset($username, $name, $email, $phone, $website)) {
            throw new HttpException(400, "Error receiving all user fields");
        }

        $data = $this->service->createUser($username, $name, $email, $phone, $website);

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[Route('/delete_user', name: 'delete_user')]
    public function deleteUser(Request $request): Response
    {
        if (!$request->get("id")) {
            throw new HttpException(400, "Error receiving user id");
        }

        $data = $this->service->deleteUser($request->get("id"));

        return new JsonResponse($data);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/server_side_processing', name: 'server_side_processing')]
    public function serverSideProccessing(): Response
    {
        $data = $this->service->fetchUsersFromApi();

        $response = [
            "draw" => 1,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        ];

        return new JsonResponse($response);
    }
}
