<?php

namespace Livefyre;


class NetworkTest extends \PHPUnit_Framework_TestCase {
    private $config;

    protected function setUp() {
        $this->config = new LfTest();
        $this->config->setPropValues("prod");
    }

    public function testApi() {
        $network = Livefyre::getNetwork($this->config->NETWORK_NAME, $this->config->NETWORK_KEY);
        $network->setUserSyncUrl("url/{id}");
        $network->syncUser("user-name_123.BLAH");
    }

    /**
     * @covers Livefyre\Core\Network::setUserSyncUrl
     * @expectedException \InvalidArgumentException
     */
    public function testNetworkUserSyncUrl() {
        $network = Livefyre::getNetwork($this->config->NETWORK_NAME, $this->config->NETWORK_KEY);
        $network->setUserSyncUrl("www.test.com");
    }

    /**
     * @covers Livefyre\Core\Network::buildUserAuthToken
     * @expectedException \InvalidArgumentException
     */
    public function testNetworkBuildUserAuthToken() {
        $network = Livefyre::getNetwork($this->config->NETWORK_NAME, $this->config->NETWORK_KEY);
        $network->buildUserAuthToken("fawe-f@-fawef.", "test", "test");
    }

    /**
     * @covers Livefyre\Core\Network::validateLivefyreToken
     */
    public function testNetworkValidateLivefyreToken() {
        $network = Livefyre::getNetwork($this->config->NETWORK_NAME, $this->config->NETWORK_KEY);
        $network->validateLivefyreToken($network->buildLivefyreToken());
    }

    /**
     * @covers Livefyre\Core\Network::validateLivefyreToken
     * @expectedException \InvalidArgumentException
     */
    public function testNetworkValidateLivefyreToken_fail() {
        $network = Livefyre::getNetwork($this->config->NETWORK_NAME, $this->config->NETWORK_KEY);
        $network->validateLivefyreToken("");
    }

    /**
     * @covers Livefyre\Validator\NetworkValidator::validate
     * @expectedException \InvalidArgumentException
     */
    public function testInit_nullName() {
        Livefyre::getNetwork(NULL, $this->config->SITE_KEY);
    }

    /**
     * @covers Livefyre\Validator\NetworkValidator::validate
     * @expectedException \InvalidArgumentException
     */
    public function testInit_badName() {
        Livefyre::getNetwork("livefyre", $this->config->SITE_KEY);
    }

    /**
     * @covers Livefyre\Validator\NetworkValidator::validate
     * @expectedException \InvalidArgumentException
     */
    public function testInit_badKey() {
        Livefyre::getNetwork($this->config->SITE_ID, "");
    }

    public function testGetUrn() {
        $network = Livefyre::getNetwork($this->config->NETWORK_NAME, $this->config->NETWORK_KEY);
        $urn = $network->getUrn();
        $this->assertEquals("urn:livefyre:" . $this->config->NETWORK_NAME, $urn);
    }
}
