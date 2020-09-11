<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class RegisterCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:register';
    private $passwordEncoder;
    private $container;


    public function __construct(UserPasswordEncoderInterface $passwordEncoder, ContainerInterface $container)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->container = $container;
        parent::__construct();

    }


    public function execute(InputInterface $input, OutputInterface $output)
    {

        $helper = $this->getHelper('question');

        $question1 = new Question('User Email : ');

        $question2 = new Question('User Name : ');

        $question3 = new ChoiceQuestion(
            'Please Select User Role (defaults to ROLE_SPECIALIST )',
            ['ROLE_ADMIN', 'ROLE_SPECIALIST'],
            1
        );
        $question3->setErrorMessage('Role %s Is Invalid.');

        $question4 = new Question('User Password : ');
        $question4->setHidden(true);
        $question4->setHiddenFallback(false);

        $question5 = new Question('User Repeated Password : ');
        $question5->setHidden(true);
        $question5->setHiddenFallback(false);


        $email = $helper->ask($input, $output, $question1);
        $name = $helper->ask($input, $output, $question2);
        $role = $helper->ask($input, $output, $question3);
        $password = $helper->ask($input, $output, $question4);
        $repassword = $helper->ask($input, $output, $question5);

        if ($password !== $repassword) {
            throw new \Exception('The Passwords Does Not Match!');

        }

        $user = new User();
        $user->setEmail($email);
        $user->setName($name);
        $user->setRoles([$role]);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        $user->setIsWorking(false);


        // Save
        $em = $this->container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $output->writeln([
            '============',
            'User Registered',
            '============',
            'Name : ' . $name,
            'Email : ' . $email,
            'Role : ' . $role,
            '',
        ]);
        return Command::SUCCESS;
    }
}