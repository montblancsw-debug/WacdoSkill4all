<?php

namespace App\Tests\Controller;

use App\Entity\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MenuControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;

    /** @var EntityRepository<Menu> */
    private EntityRepository $menuRepository;
    private string $path = '/admin/menu/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->menuRepository = $this->manager->getRepository(Menu::class);

        foreach ($this->menuRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Menu index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'menu[menu_nom]' => 'Testing',
            'menu[menu_description]' => 'Testing',
            'menu[menu_prix]' => 'Testing',
            'menu[menu_status]' => 'Testing',
            'menu[menu_image]' => 'Testing',
            'menu[produits]' => 'Testing',
        ]);

        self::assertResponseRedirects('/admin/menu');

        self::assertSame(1, $this->menuRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new Menu();
        $fixture->setMenuNom('My Title');
        $fixture->setMenuDescription('My Title');
        $fixture->setMenuPrix('My Title');
        $fixture->setMenuStatus('My Title');
        $fixture->setMenuImage('My Title');
        $fixture->setProduits('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Menu');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new Menu();
        $fixture->setMenuNom('Value');
        $fixture->setMenuDescription('Value');
        $fixture->setMenuPrix('Value');
        $fixture->setMenuStatus('Value');
        $fixture->setMenuImage('Value');
        $fixture->setProduits('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'menu[menu_nom]' => 'Something New',
            'menu[menu_description]' => 'Something New',
            'menu[menu_prix]' => 'Something New',
            'menu[menu_status]' => 'Something New',
            'menu[menu_image]' => 'Something New',
            'menu[produits]' => 'Something New',
        ]);

        self::assertResponseRedirects('/admin/menu');

        $fixture = $this->menuRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getMenuNom());
        self::assertSame('Something New', $fixture[0]->getMenuDescription());
        self::assertSame('Something New', $fixture[0]->getMenuPrix());
        self::assertSame('Something New', $fixture[0]->getMenuStatus());
        self::assertSame('Something New', $fixture[0]->getMenuImage());
        self::assertSame('Something New', $fixture[0]->getProduits());

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new Menu();
        $fixture->setMenuNom('Value');
        $fixture->setMenuDescription('Value');
        $fixture->setMenuPrix('Value');
        $fixture->setMenuStatus('Value');
        $fixture->setMenuImage('Value');
        $fixture->setProduits('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/admin/menu');
        self::assertSame(0, $this->menuRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
