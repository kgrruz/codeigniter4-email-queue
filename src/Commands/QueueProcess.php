<?php

namespace EmailQueue\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\CLIRequest;
use EmailQueue\Models\EmailQueueModel;
use CodeIgniter\I18n\Time;

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
            $queue = new EmailQueueModel();
            $emails = $queue->getBatch($request->getOption('l'));

            helper('setting');

            $totalSteps = count($emails);
            $currStep   = 0;

            CLI::write('Emails to send: '.$totalSteps, 'green');

            $email = \Config\Services::email();

            $config['protocol'] = setting('Email.protocol');

            if(setting('Email.protocol') == 'sendmail'){
              $config['mailpath'] = '/usr/sbin/sendmail';
            }

            $config['charset']  = 'iso-8859-1';
            $config['wordWrap'] = true;

            if(setting('Email.protocol') == 'smtp'){

                $config['SMTPHost'] = setting('Email.SMTPHost');
                $config['SMTPPort'] = setting('Email.SMTPPort');
                $config['SMTPUser'] = setting('Email.SMTPUser');
                $config['SMTPPass'] = setting('Email.SMTPPass');
                $config['SMTPCrypto'] = setting('Email.SMTPCrypto');
                $config['SMTPTimeout'] = setting('Email.SMTPTimeout');
                $config['SMTPKeepAlive'] = setting('Email.SMTPKeepAlive');

              }

            $email->initialize($config);

            $email->setFrom(setting('Email.fromEmail'), setting('Email.fromName'));

            foreach($emails as $e){

                $email->setTo($e['email']);
                $email->setSubject($e['subject']);
                $email->setMessage($e['message']);

                if (!$email->send()){

                  CLI::write('Cold not send email to: '.$e['email'], 'light_red');

                  $update_data = array(
                    'attempts'=>$e['attempts']+1,
                  );

                }else{

                  $update_data = array(
                    'send_at'=>new Time('now'),
                    'sent'=>1,
                    'attempts'=>$e['attempts']+1,
                  );
              }

              $queue->update($e['id'],$update_data);

              CLI::showProgress($currStep++, $totalSteps);

            }


    }
}
