<?php namespace util\data\unittest;

use util\cmd\Console;
use peer\server\Server;
use lang\Throwable;

class Worker extends \lang\Object {

  /**
   * Multiplies numbers given on STDIN with a factor given as argument.
   * Empty input ends command.
   *
   * @param  string[] $args
   * @return int
   */
  public static function main($args) {
    $server= new Server('127.0.0.1', 0);
    $server->setProtocol(newinstance('peer.server.ServerProtocol', [$args ? (int)$args[0] : 2], '{
      public function __construct($factor) {
        $this->factor= $factor;
      }

      public function initialize() { }

      public function handleConnect($socket) { }

      public function handleDisconnect($socket) { }

      public function handleError($socket, $e) { }

      public function handleData($socket) {
        $in= $socket->readLine();

        if ("SHUTDOWN" === $in) {
          $this->server->terminate= true;
        } else if ($in) {
          $socket->write(serialize(unserialize($in) * $this->factor)."\n");
        } else {
          $socket->write("-ERR\n");
        }
      }  
    }'));

    try {
      $server->init();
      Console::writeLinef('+ %s:%d', $server->socket->host, $server->socket->port);
      $server->service();
      Console::writeLine('+ Shutdown');
      return 0;
    } catch (Throwable $e) {
      Console::writeLine('- ', $e->getMessage());
      return 1;
    }
  }
}