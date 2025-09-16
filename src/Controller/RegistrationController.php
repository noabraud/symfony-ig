<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Repository\UserRepository;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($this->getUser()) {
            return $this->redirectToRoute('app_main'); 
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();
            $locale = $request->getLocale();
            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('noabraud@gmail.com', $translator->trans('validation_mail.bot', [], 'messages', $locale)))
                    ->to((string) $user->getEmail())
                    ->subject($translator->trans('validation_mail.subject', [], 'messages', $locale))
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                'locale' => $locale, 
            ])
            );

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_check_email_registration');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id'); // retrieve the user id from the url
        // Verify the user id exists and is not null
        if (null === $id) {
          return $this->redirectToRoute('app_main');
              }

        $user = $userRepository->find($id);
        // Ensure the user exists in persistence
        if (null === $user) {
            return $this->redirectToRoute('app_main');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $message = $translator->trans('email_verification.success', [], 'messages');
        $this->addFlash('success', $message);

        return $this->redirectToRoute('app_login');
    }

    #[Route('/check-email-registration', name: 'app_check_email_registration')]
    public function checkEmail(): Response
    {
        return $this->render('registration/check_email.html.twig');
    }

}
