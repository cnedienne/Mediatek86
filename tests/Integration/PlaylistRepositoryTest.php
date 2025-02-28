<?php

namespace App\Tests\Repository;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlaylistRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?PlaylistRepository $playlistRepository = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->playlistRepository = $this->entityManager->getRepository(Playlist::class);
    }

    public function testAddPlaylist(): void
    {
        $playlist = new Playlist();
        $playlist->setName('Test Playlist');

        $this->playlistRepository->add($playlist);

        $this->assertNotNull($playlist->getId());
    }

    public function testRemovePlaylist(): void
    {
        $playlist = new Playlist();
        $playlist->setName('Test Playlist');

        $this->playlistRepository->add($playlist);
        $id = $playlist->getId();

        $this->playlistRepository->remove($playlist);

        $this->assertNull($this->playlistRepository->find($id));
    }

    public function testFindAllOrderByAmount(): void
    {
        $playlists = $this->playlistRepository->findAllOrderByAmount('ASC');
        $this->assertIsArray($playlists);
    }

    public function testFindAllOrderByName(): void
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $this->assertIsArray($playlists);
    }

    public function testFindByContainValue(): void
    {
        $playlists = $this->playlistRepository->findByContainValue('name', 'Test');
        $this->assertIsArray($playlists);
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