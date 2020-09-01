<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;


class RegisterCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:register';
    private $passwordEncoder;
    private $container;


    public function __construct(UserPasswordEncoderInterface $passwordEncoder,ContainerInterface $container)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->container = $container;
        parent::__construct();

    }

    protected function configure()
    {
        $this
        // configure an argument
        ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('firstName', InputArgument::REQUIRED, 'The first name of the user.')
            ->addArgument('lastName', InputArgument::REQUIRED, 'The last name of the user.')
            ->addArgument('role', InputArgument::REQUIRED, 'The role of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.')

    ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
{
    $user = new User();


    $user->setEmail($input->getArgument('email'));
    $user->setName($input->getArgument('firstName').' '.$input->getArgument('lastName'));
    $user->setPassword($this->passwordEncoder->encodePassword($user, $input->getArgument('password')));
    $user->setRoles([$input->getArgument('role')]);
    
    // Save
    $em = $this->container->get('doctrine')->getManager();
    $em->persist($user);
    $em->flush();

    $output->writeln([
        '============',
        'User Registered',
        '============',
        'Name : '.$input->getArgument('firstName').' '.$input->getArgument('lastName') ,
        'Email : '.$input->getArgument('email') ,
        'Role : '.$input->getArgument('role') ,
        '',
    ]);
    return Command::SUCCESS;
}
}