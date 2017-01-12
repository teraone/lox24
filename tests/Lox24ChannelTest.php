<?php

namespace NotificationChannels\Lox24\Test;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Notifications\Notification;
use \Mockery;
use NotificationChannels\Lox24\Lox24;
use NotificationChannels\Lox24\Lox24Channel;
use NotificationChannels\Lox24\Lox24Message;

class Lox24ChannelTest extends \PHPUnit_Framework_TestCase
{
    public $responseSuccess = '<?xml version="1.0" encoding="iso-8859-1" ?><answer>
                                 <code>100</code>
                                 <codetext>SMS erfolgreich versendet</codetext>
                                 <info>
                                 <MSGID>e4da3b7fbbce2345d7772b0674a318d5</MSGID>
                                 <Text>Testtext</Text>
                                 <Zeichen>8</Zeichen>
                                 <SMS>1</SMS>
                                 <Absenderkennung>0049(160)123456</Absenderkennung>
                                 <Ziel>+49(160)654321</Ziel>
                                 <Kosten>0,040</Kosten>
                                 <Versenden>Sofort</Versenden>
                                 </info></answer>';

    public $responseError = '<?xml version="1.0" encoding="iso-8859-1" ?><answer>
                                 <code>200</code>
                                 <codetext>SMS erfolgreich versendet</codetext>
                                 <info>
                                 <MSGID>e4da3b7fbbce2345d7772b0674a318d5</MSGID>
                                 <Text>Testtext</Text>
                                 <Zeichen>8</Zeichen>
                                 <SMS>1</SMS>
                                 <Absenderkennung>0049(160)123456</Absenderkennung>
                                 <Ziel>+49(160)654321</Ziel>
                                 <Kosten>0,040</Kosten>
                                 <Versenden>Sofort</Versenden>
                                 </info></answer>';

    public function tearDown()
    {
        Mockery::close();
    }


    /** @test */
    public function canSendNotification()
    {
        $client = Mockery::mock(Client::class);

        $client->shouldReceive('post')->once()
            ->with(
                'https://www.lox24.eu/API/httpsms.php'
                ,
                [
                    'form_params'    => [
                        'service'    => 3325,
                        'text'       => 'hello',
                        'to'         => '12345789',
                        'encoding'   => 0,
                        'from'       => 'lox24test',
                        'timestamp'  => 0,
                        'return'     => 'xml',
                        'httphead'   => 0,
                        'action'     => 'send',
                        'account'     => '123456',
                        'passwordHash'=> md5(123456),
                    ],
                ]
            )->andReturn(new Response(200,[],$this->responseSuccess));

        $lox24 = new Lox24('123456', 123456, $client);

        $channel = new Lox24Channel($lox24);
        $channel->send(new TestNotifiable(), new TestNotification());
    }

}


class TestNotifiable
{
    public function routeNotificationFor()
    {
        return '';
    }
}
class TestNotification extends Notification
{
    public function toLox24()
    {
        return Lox24Message::create('hello')->setTo('12345789')->setFrom('lox24test');
    }
}

