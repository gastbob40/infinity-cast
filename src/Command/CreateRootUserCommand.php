<?php


namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateRootUserCommand extends Command
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, UserRepository $userRepository)
    {
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    protected function configure()
    {
        $this
            ->setName('cast:user:root')
            ->setDescription('Add a root permission to a user')
            ->addArgument('email', InputArgument::REQUIRED, 'Your account email');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');

        $io = new SymfonyStyle($input, $output);

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user == null) {
            $io->error("Cannot found user with the login '" . $email . "'.");
            return;
        }

        $roles = $user->getRoles();
        $roles[] = 'ROLE_ADMIN';
        $user->setRoles($roles);
        $this->em->flush();

        $io->success('I just add "' . $email . '" as a root user');
    }
}
