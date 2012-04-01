<?php

class ArticleFacadeTest extends UnitTestCase
{

	/** @var \PHPUnit_Framework_MockObject_MockObject */
	private $repository;

	/** @var \ArticleFacade */
	private $facade;

	protected function setUp()
	{
                $this->repository = Mockista\mock('ArticleRepository');

		$this->facade = new ArticleFacade($this->repository);
	}

	public function testGetLastArticles()
	{
		$article = $this->createArticle();
		 $this->repository->findAll()->AndReturn(array($article));
		$this->repository->freeze();

		$articles = $this->facade->getLastArticles();    
		$this->assertCount(1, $articles);
		$this->assertEquals($article, $articles[0]);
		$this->repository->assertExpectations();
	}

	public function testGetArticleById()
	{
		$article = $this->createArticle();
		$this->repository->findById(1)->once()->AndReturn($article);
		$this->repository->freeze();

		$this->assertEquals($article, $this->facade->getArticleById(1));
	}

	public function testIncreaseSeen()
	{
		$article = $this->createArticle();

		$article2 = clone $article;
		$article2->setSeen($article2->getSeen() + 1);
		//jelikoz predane parametry se v mockistovi hashuji, tak nastane problem, 
		//pokud se dany objekt mezi prirazenim jako parametrem mockovane metody a samotnym zavolanim metody                
		$this->repository->persist($article2);
		$this->repository->freeze();

		$this->facade->increaseSeen($article);
		$this->assertEquals(4, $article->getSeen());
	}

	/**
	 * @return \Article
	 */
	private function createArticle()
	{
		return new Article(1, 'aaa', 'bbb', 3);
	}

}
