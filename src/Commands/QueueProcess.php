<?php

namespace EmailQueue\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\HTTP\CLIRequest;

use EmailQueue\Models\EmailQueueModel;
use EmailQueue\EmailQueue;

class QueueProcess extends BaseCommand
{
    protected $group       = 'Emails';
    protected $name        = 'emails:send';
    protected $description = 'Process emails queue.';
    protected $options =  array(
      '-l' => 'Set the number of unsent emails to process',
    );


    public function run(array $params)
    {
      $request = service('CLIRequest');
      EmailQueue::process($request->getOption('l'));
    }
}
