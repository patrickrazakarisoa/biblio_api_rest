<?php

namespace App\DataFixtures;

use App\Entity\Livre;
use App\Entity\Pret;
use App\Entity\Adherent;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $manager;
    private $faker;
    private $repoLivre;
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoderInterface)
    {
        $this->faker = Factory::create("fr_FR");
        $this->passwordEncoder = $userPasswordEncoderInterface;
    }
    
    public function load(ObjectManager $manager)
    {

        $this->manager = $manager;
        $this->repoLivre = $this->manager->getRepository(Livre::class);
        $this->loadAdherent();
        $this->loadPret();

        $manager->flush();
    }

   /**
    * création des adhérents
    *
    * @return void
    */
    public function loadAdherent(){

        $genre=['male','female'];
        $commune=[ "78003","78021","78015","78084","78085","78006","78084","78007","78010","78041","78012","78032","78017"];

        for($i=0;$i<25;$i++){
            $adherent= new Adherent();
            $adherent   ->setNom($this->faker->lastName())  
                        ->setPrenom($this->faker->firstName($genre[mt_rand(0,1)]))
                        ->setAdresse($this->faker->streetAddress())
                        ->setTelephone($this->faker->phoneNumber())
                        ->setCodeCommune($commune[mt_rand(0,sizeof($commune)-1)])
                        ->setMail(strtolower($adherent->getNom(). "@gmail.com"))
                        ->setPassword($this->passwordEncoder->encodePassword($adherent,$adherent->getNom())); 
            $this->addReference("adherent".$i, $adherent);
            $this->manager->persist($adherent);                       
        }

        $adherentAdmin= new Adherent();
        $rolesAdmin[] = ADHERENT::ROLE_ADMIN;
        $adherentAdmin   ->setNom("Raz")
                    ->setPrenom("Dada")
                    ->setMail("admin@gmail.com")
                    // ->setPassword("raz")
                    ->setPassword($this->passwordEncoder->encodePassword($adherentAdmin,$adherentAdmin->getNom()))
                    ->setRoles($rolesAdmin);
        $this->manager->persist($adherentAdmin);

        $adherentManager= new Adherent();
        $rolesManager[] = ADHERENT::ROLE_MANAGER;
        $adherentManager   ->setNom("Durant")
                    ->setPrenom("Sophie")
                    ->setMail("manager@gmail.com")
                    // ->setPassword("durant")
                    ->setPassword($this->passwordEncoder->encodePassword($adherentManager,$adherentManager->getNom()))
                    ->setRoles($rolesManager);
        $this->manager->persist($adherentManager);

        $this->manager->flush();
    }

    /**
     * création des prêts
     *
     * @return void
     */
    public function loadPret(){
        for($i=0;$i<25;$i++){
            $max=mt_rand(1,5);
            for($j=0;$j<=$max;$j++){
                $pret = new Pret();
                $livre = $this->repoLivre->find(mt_rand(1,50));
                $pret   ->setLivre($livre)
                        ->setAdherent($this->getReference("adherent".$i))
                        ->setDatePret($this->faker->dateTimeBetween('-6 months'));
                $dateRetourPrevue = date('Y-m-d H:m:n', strtotime('15 days', $pret->getDatePret()->getTimestamp()));
                $dateRetourPrevue = \DateTime::createFromFormat('Y-m-d H:m:n', $dateRetourPrevue);
                $pret   ->setDateRetourPrevue($dateRetourPrevue);

                    if(mt_rand(1,3)==1){
                        $pret->setDateRetourReelle($this->faker->dateTimeInInterval($pret->getDatePret(), "+30 days"));
                    }
                $this->manager->persist($pret);
            }

            $this->manager->flush();
        }
    }
}
