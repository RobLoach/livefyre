<?php

namespace Livefyre;


use JWT;

use Livefyre\Core\Core;
use Livefyre\Dto\Topic;

class CollectionTest extends \PHPUnit_Framework_TestCase {
    private $config;
    private $site;

    protected function setUp() {
        $this->config = new LfTest();
        $this->config->setPropValues("prod");
        $this->site = Livefyre::getNetwork($this->config->NETWORK_NAME, $this->config->NETWORK_KEY)->getSite($this->config->SITE_ID, $this->config->SITE_KEY);
    }

    public function testApi() {
        $name = "PHPCreateCollection" . time();

        $collection = $this->site->buildCommentsCollection($name, $name, "http://answers.livefyre.com/PHP");
        $collection->createOrUpdate();

        $id = $collection->getCollectionContent()->{"collectionSettings"}->{"collectionId"};
        $this->assertEquals($id, $collection->getData()->getId());

        $id = $collection->getData()->getId();
        $collection->getData()->setId(NULL);
        $collection->createOrUpdate();

        $this->assertEquals($id, $collection->getData()->getId());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBuildCollection_badParam() {
        $this->site->buildCollection(NULL, NULL, NULL, NULL);
    }

	/**
     * @covers Livefyre\Validator\CollectionValidator::validate
     * @expectedException \InvalidArgumentException
	 */
    public function testBuildCollection_badUrl() {
    	$this->site->buildCommentsCollection("title", "articleId", "url");
    }

	/**
     * @covers Livefyre\Validator\CollectionValidator::validate
     * @expectedException \InvalidArgumentException
	 */
    public function testBuildCollection_badTitle() {
        $this->site->buildCommentsCollection("1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456", "articleId", "http://www.url.com");
    }

    /**
     * @covers Livefyre\Validator\CollectionValidator::validate
     * @expectedException \InvalidArgumentException
     */
    public function testBuildCollection_badType() {
        $this->site->buildCollection("bad type", "title", "articleId", "http://livefyre.com");
    }

    public function testBuildCollectionMetaToken() {
        $collection = $this->site->buildCommentsCollection("title", "articleId", "https://www.url.com");
        $this->assertNotNull($collection->buildCollectionMetaToken());

        $collection->getData()->setTags("tags");

        $token = $collection->buildCollectionMetaToken();
        $decoded = JWT::decode($token, $this->config->SITE_KEY, array(Core::ENCRYPTION));

        $this->assertEquals("tags", $decoded->{"tags"});
    }

    public function testBuildChecksum() {
        $collection = $this->site->buildCommentsCollection("title", "articleId", "http://livefyre.com");
        $collection->getData()->setTags("tags");

        $checksum = $collection->buildChecksum();

        $this->assertEquals("8bcfca7fb2187b1dcb627506deceee32", $checksum);
    }

    public function testNetworkIssued() {
        $collection = $this->site->buildCommentsCollection("title", "articleId", "http://livefyre.com");
        $this->assertFalse($collection->isNetworkIssued());

        $collection->getData()->setTopics(array(Topic::create($this->site, "ID", "LABEL")));
        $this->assertFalse($collection->isNetworkIssued());

        $collection->getData()->setTopics(array(Topic::create($this->site->getNetwork(), "ID", "LABEL")));
        $this->assertTrue($collection->isNetworkIssued());

    }
}
