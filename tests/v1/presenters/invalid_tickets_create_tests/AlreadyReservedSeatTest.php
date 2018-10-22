<?php

use App\Tests\BaseTestCase;
use App\v1Module\Models\Seats;
use Nette\Application\BadRequestException;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;
use Nette\Utils\DateTime;
use Tester\Assert;

$container = require __DIR__ . "/../../../bootstrap.php";

class AlreadyReservedSeatTest extends BaseTestCase {

    /** @var Presenter */
    protected $presenter;

    public function setUp() {
        $this->database->table('seats')->get(50)->update([
            'state' => Seats::RESERVED
        ]);

        $this->setUpRequestInput(array(
            'user_id' => 1,
            'seats' => [50],
            'created_at' => DateTime::from("2017-01-01 00:00:00"),
            'updated_at' => DateTime::from("2017-01-01 00:00:00"),
        ));

        $factory = $this->container->getByType(IPresenterFactory::class);

        $this->presenter = $factory->createPresenter('v1:Tickets');
        $this->presenter->autoCanonicalize = false;
    }

    public function testAlreadyReservedSeatCreate() {
        $request = new Request('v1:Tickets', 'POST', array('action' => 'create', 'token' => 'abcd'));
        
        Assert::exception(function() use ($request) {
            $response = $this->presenter->run($request);
        }, BadRequestException::class);
    }

}

# Spuštění testovacích metod
run(new AlreadyReservedSeatTest($container));
