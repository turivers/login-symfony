<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController
{
    private $session;
    private $security;
    const TOTAL_BLOCKED_TIME = 300;

    public function __construct(SessionInterface $session, Security $security)
    {
        $this->session = $session;
        $this->security = $security;
    }

    /**
     * @Route("/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_user');
        }
        
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        $is_blocked = false;
        $blocked_time_left = $this->get_blocked_time_left();

        if ($blocked_time_left) {
            if ($blocked_time_left > 0) {
                $is_blocked = true;
            } else {
                $this->session->remove('error_count');
                $this->session->remove('start_blocked_time');
            }
        }

        if ($error) {
            $this->count('increment');
            if ($this->count() >= 3) {
                $this->session->set('start_blocked_time', $_SERVER['REQUEST_TIME']);
                $is_blocked = true;
            }
        }

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $is_blocked ? $this->get_error_blocked_message() : $error,
                'disabled' => $is_blocked ? 'disabled' : '',
            ]);
    }

    /**
     * @Route("/profile", name="app_user")
     */
    public function profile()
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $this->session->remove('error_count');
        $this->session->remove('start_blocked_time');
        
        return $this->render('security/user.html.twig', ['username' => $user->getFullname()]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    public function get_blocked_time_left()
    {
        if (!$this->session->get('start_blocked_time')) {
            return false;
        }
        
        return $this::TOTAL_BLOCKED_TIME - ($_SERVER['REQUEST_TIME'] - $this->session->get('start_blocked_time'));
    }

    public function count($increment = '')
    {   
        $c = $this->session->get('error_count');
        
        if ($increment !== 'increment') {
            return $c;
        }

        $c++;
        $this->session->set('error_count', $c);
    }

    public function get_error_blocked_message()
    {
        return $error_blocked = [
            'MessageKey' => 'Try again after N seconds.',
            'MessageData' => ['{n}' => $this->get_blocked_time_left()],
        ];
    }
}
