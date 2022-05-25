<?php

namespace App\Controller;

use App\Entity\User;
use App\Factory\RiderFactory;
use App\Form\RegistrationFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        MailerInterface $mailer,
        string $notificationEmail
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);
            $user->setRider(RiderFactory::createFromUser($user));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                ->to($user->getEmail())
                ->subject('Witamy w systemie Wakepark Wrocław')
                ->htmlTemplate('emails/register.html.twig')
                ->context([
                    'name' => $user->getFirstname(),
                ]);

            $mailer->send($email);

            $adminEmail = (new TemplatedEmail())
                ->to($notificationEmail)
                ->subject('Nowy user na Wakepark Wrocław')
                ->htmlTemplate('emails/new_user.html.twig')
                ->context([
                    'user_email' => $user->getEmail(),
                ]);

            $mailer->send($adminEmail);

            $this->addFlash(
                'notice',
                'Your account has been created successfully! You can now log in.'
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
