<?php

class ArticleRepositoryCachedTest extends UnitTestCase
{

	/** @var \PHPUnit_Framework_MockObject_MockObject */
	private $repository;

	/** @var \PHPUnit_Framework_MockObject_MockObject */
	private $cache;

	/** @var \ArticleRepositoryCached */
	private $cachedRepository;

	protected function setUp()
	{
		$this->repository = Mockista\mock('ArticleRepository');

		$this->cache = Mockista\mock('Nette\Caching\Cache');

		$this->cachedRepository = new ArticleRepositoryCached($this->repository, $this->cache);
	}

	public function testFindById()
	{
		$article = $this->createArticle();
		$this->repository->findById(1)->once()->AndReturn($article);
		$this->repository->freeze();

		$this->cache->offsetExists('article-1')->once();
		$this->cache->save('article-1', $article)->once();
		$this->cache->freeze();

		$article = $this->cachedRepository->findById(1);
		$this->assertEquals($article, $article);
	}

	public function testFindByIdCached()
	{
		$article = $this->createArticle();

		$this->cache->offsetExists('article-1')->AndReturn(TRUE);
		$this->cache->offsetGet('article-1')->AndReturn($article);
		$this->cache->freeze();

		$this->repository->findById()->never();
		$this->repository->freeze();

		$this->assertEquals($article, $this->cachedRepository->findById(1));
	}

	public function testPersist()
	{
		$article = $this->createArticle();

		$this->repository->persist($article)->once();
		$this->repository->freeze();

		$this->cache->save('article-1', $article)->once();
		$this->cache->freeze();

		$this->assertInstanceOf('ArticleRepository', $this->cachedRepository->persist($article));
	}

	/**
	 * @return \Article
	 */
	private function createArticle()
	{
		return new Article(1, 'aaa', 'bbb', 3);
	}

}
