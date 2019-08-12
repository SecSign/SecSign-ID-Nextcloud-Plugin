<?php
/**
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
namespace OCA\SecSignID\Tests\Unit\Services;

use OCP\IRequest;
use OCP\IUserManager;
use OCP\IGroupManager;

use OCA\SecSignID\Service\ConfigService;
use OCA\SecSignID\Exceptions\InvalidInputException;
use Test\TestCase;

class ConfigServiceTest extends \PHPUnit_Framework_TestCase {
    
    private $container;
    private $userId = 'testid';
    private $config;
    private $permission;
    private $values;
    private $manager;
    private $groupmanager;

    protected function setUp(){
        parent::setUp();

        $app = new \OCA\SecSignID\AppInfo\Application();
        $this->container = $app->getContainer();
        $request = $this->createMock(IRequest::class);
        $this->manager = $userManager = $this->createMock(IUserManager::class);
        $this->groupmanager = $groupManager = $this->createMock(IGroupManager::class);
        $this->permission = $permission = $this->getMockBuilder('OCA\SecSignID\Service\PermissionService')
            ->disableOriginalConstructor()
            ->getMock();
        $this->config = new ConfigService(
            'secsignid', $request, $this->userId,                                     $userManager, $this->permission, $groupManager
        );
    }

    public function testSaveServer(){
        $server = [
            'server' => 'server',
            'fallback' => 'fallback',
            'serverport' => '12345',
            'fallbackport' => '12345'
        ];
        $this->assertTrue($this->config->saveServer($server));
    }

    public function testSaveServerInvalidInput(){
        $server = [
            'server' => 'server',
            'fallback' => 'fallback',
            'serverport' => '65536',
            'fallbackport' => '12345'
        ];
        $this->expectException(InvalidInputException::class);
        $this->config->saveServer($server);
    }

    public function testSaveServerMobile(){
        $server = 'serverurl';
        $this->assertTrue($this->config->saveServerMobile($server));
    }

    /**
     * Check if user can edit when edit is enabled for all users
     */
    public function testCanUserEdit(){
        $this->permission->method('getAppValue')
                ->withConsecutive(
                    ["allowEdit", false],
                    ["allowGroups", false],
                    ["allowEditGroups", '{}']
                )
                ->will($this->onConsecutiveCalls(true, false, []));
        $this->manager->expects($this->any())
                ->method('get')
                ->with($this->userId)
                ->willReturn('user');
        $this->groupmanager->expects($this->any())
                ->method('getUserGroupIds')
                ->with('user')
                ->willReturn(['admin', 'no group']);
        $this->assertTrue($this->config->canUserEdit());
    }

    public function testSaveServerMobileInvalidInput(){
        $server = '';
        $this->expectException(InvalidInputException::class);
        $this->config->saveServerMobile($server);
    }

    public function testGetServer(){
        // $this->permission->expects($this->once())
        //         ->method('getAppValue')
        //         ->with($this->equalTo('fallbackport'), $this->equalTo(443))
        //         ->willReturn($this->equalTo(100));
        // $this->permission->expects($this->once())
        //         ->method('getAppValue')
        //         ->with($this->equalTo('serverport'), $this->equalTo(443))
        //         ->willReturn($this->equalTo(100));
        // $this->permission->expects($this->once())
        //         ->method('getAppValue')
        //         ->with($this->equalTo('fallback'), $this->equalTo("https://httpapi2.secsign.com"))
        //         ->willReturn($this->equalTo('fallback'));
        // $this->permission->expects($this->once())
        //         ->method('getAppValue')
        //         ->with($this->equalTo('server'), $this->equalTo("https://httpapi.secsign.com"))
        //         ->willReturn($this->equalTo('server'));
        $this->permission->method('getAppValue')
                ->withConsecutive(
                    [$this->equalTo('server'), $this->equalTo("https://httpapi.secsign.com")],
                    [$this->equalTo('fallback'), $this->equalTo("https://httpapi2.secsign.com")],
                    [$this->equalTo('serverport'), $this->equalTo(443)],
                    [$this->equalTo('fallbackport'), $this->equalTo(443)]
                )
                ->will($this->onConsecutiveCalls('server','fallback',100, 200));
        
        
        $server = [
            'server' => 'server',
            'fallback' => 'fallback',
            'serverport' => 100,
            'fallbackport' => 200
        ];
        $this->assertEquals($server, $this->config->getServer());
    }

    public function testGetServerMobile(){
        $server = 'server';
        $this->permission->expects($this->once())
                ->method('getAppValue')
                ->with('mobileurl', 'id1.secsign.com')
                ->willReturn('server');
        $this->assertEquals($server, $this->config->getServerMobile());
    }


    
}