<?php
/**
 * Created by PhpStorm.
 * User: Lew
 * Date: 09/05/2017
 * Time: 15:36
 */

namespace Lew\STBundle\Command;

use Lew\STBundle\Entity\Image;
use Lew\STBundle\Entity\Post;
use Lew\STBundle\Entity\Video;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;
use Lew\UserBundle\Entity\User;
use Lew\STBundle\Entity\Category;
use Lew\STBundle\Entity\Trick;


class LoadCommand extends Command implements ContainerAwareInterface
{

    private $container;


    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setName('lew:load')
            ->setDescription('Load data in Database')
            ->setHelp('This command allows you to load data into the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        $repoCat = $this->container->get('doctrine')->getManager()->getRepository('LewSTBundle:Category');
        $repoTrick = $this->container->get('doctrine')->getManager()->getRepository('LewSTBundle:Trick');
        $repoUser = $this->container->get('doctrine')->getManager()->getRepository('LewUserBundle:User');
        $date = new \DateTime();

        $output->writeln([
            '============',
            'User Creator',
            '============',
        ]);
        $value = Yaml::parse(file_get_contents(__DIR__.'/file.yml'));

        foreach ($value['users'] as $utilisateur) {
            $user = new User();
            $user->setEnabled(true);
            $user->setEmail($utilisateur['mail']);
            $user->setUsername($utilisateur['username']);
            $user->setRoles(array($utilisateur['role']));
            $user->setSalt(md5(uniqid()));
            $encoder = $this->container->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $utilisateur['password']);
            $user->setPassword($password);
            $user->setImageName($utilisateur['avatar']);
            $user->setUpdatedAt($date);

            $em->persist($user);

            $output->writeln($utilisateur['username']);
        }

        $output->writeln([
            '============',
            '',
            '============',
            'Category Creator',
            '============',
        ]);

        foreach ($value['categories']['name'] as $category) {
            $cat = new Category();
            $cat->setName($category);

            $em->persist($cat);

            $output->writeln($cat->getName());
        }
        $em->flush();

        $output->writeln([
            '============',
            '',
            '============',
            'Trick Creator',
            '============',
        ]);

        foreach ($value['tricks'] as $tricks) {
            $category = $repoCat->findByName($tricks['category']);


            $trick = new Trick();
            $trick->setName($tricks['name']);
            $trick->setDescription($tricks['description']);
            $trick->setCategory($category[0]);

            $em->persist($trick);

            $output->writeln($trick->getName());
        }
        $em->flush();

        $output->writeln([
            '============',
            '',
            '============',
            'Image Creator',
            '============',
        ]);

        foreach ($value['images'] as $images) {
            $trick = $repoTrick->findByName($images['trick']);

            $image = new Image();
            $image->setImageName($images['name']);
            $image->setUpdatedAt($date);
            $image->setTrick($trick[0]);

            $em->persist($image);

            $output->writeln($image->getImageName());
        }

        $output->writeln([
            '============',
            '',
            '============',
            'Video Creator',
            '============',
        ]);

        foreach ($value['videos'] as $videos) {
            $trick = $repoTrick->findByName($images['trick']);

            $video = new Video();
            $video->setUrl($videos['url']);
            $video->setTrick($trick[0]);

            $em->persist($video);

            $output->writeln($video->getPlatform().' - '.$video->getCode());
        }

        $output->writeln([
            '============',
            '',
            '============',
            'Video Creator',
            '============',
        ]);

        foreach ($value['commentaires'] as $com) {
            $trick = $repoTrick->findByName($com['trick']);
            $user = $repoUser->findByUsername($com['user']);
            $dateCom = new \DateTime($com['date']);

            $post = new Post();
            $post->setDate($dateCom);
            $post->setMessage($com['message']);
            $post->setUser($user[0]);
            $post->setTrick($trick[0]);

            $em->persist($post);

            $output->writeln($post->getMessage());
        }
        $em->flush();

        $output->write('============');
    }
}