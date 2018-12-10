<?php
namespace OCA\OwnNotes\Tests\Unit\Controller;

use PHPUnit_Framework_TestCase;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;



class IDontrollerTest extends PHPUnit_Framework_TestCase {

    protected $controller;
    protected $mapper;
    protected $userId = 'john';
    protected $request;

    public function setUp() {
        $this->request = $this->getMockBuilder('OCP\IRequest')->getMock();
        $this->mapper = $this->getMockBuilder('OCA\SecSignID\Db\NoteMapper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->controller = new IDController(
            'secsignid', $this->request, $this->mapper, $this->userId
        );
    }

    public function testCreate(){
        $id = new ID();
        $id->setUserId($this->userId);
        $id->setSecsignid('testid');
        $id->setEnabled(True);
        $this->mapper->expects($this->once())
            ->method('create')
            ->with($this->equalTo($id))
            ->will($this->returnValue($id));
    }
}