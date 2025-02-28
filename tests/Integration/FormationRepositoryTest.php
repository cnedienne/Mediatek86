<?php

namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FormationRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?FormationRepository $formationRepository = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->formationRepository = $this->entityManager->getRepository(Formation::class);
    }

    public function testAddFormationWithFutureDate()
    {
        $formation = new Formation();
        $formation->setTitle('Test Formation');
        $formation->setPublishedAt(new \DateTime('tomorrow'));

        try {
            $this->formationRepository->add($formation, true);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            return;
        }

        $this->fail('Expected exception not thrown');
    }

    public function testAddFormation(): void
    {
        $formation = new Formation();
        $formation->setTitle('Test Formation');
        $formation->setPublishedAt(new \DateTime());

        $this->formationRepository->add($formation, true);

        $this->assertNotNull($formation->getId());
    }

    public function testRemoveFormation(): void
    {
        $formation = new Formation();
        $formation->setTitle('Test Formation');
        $formation->setPublishedAt(new \DateTime());

        $this->formationRepository->add($formation, true);
        $id = $formation->getId();

        $this->formationRepository->remove($formation);

        $this->assertNull($this->formationRepository->find($id));
    }

    public function testFindAllOrderBy(): void
    {
        $formations = $this->formationRepository->findAllOrderBy('title', 'ASC');
        $this->assertIsArray($formations);
    }

    public function testFindByContainValue(): void
    {
        $formations = $this->formationRepository->findByContainValue('title', 'Test');
        $this->assertIsArray($formations);
    }

    public function testFindAllLasted(): void
    {
        $formations = $this->formationRepository->findAllLasted(5);
        $this->assertIsArray($formations);
    }

    public function testFindAllForOnePlaylist(): void
    {
        $formations = $this->formationRepository->findAllForOnePlaylist(1);
        $this->assertIsArray($formations);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if ($this->entityManager !== null) {
            $this->entityManager->close();
            $this->entityManager = null;
        }
    }
}