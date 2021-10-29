<?php

namespace EmailQueue;

use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;
use EmailQueue\Models\EmailQueueModel;

class EmailQueue{

  /**
       * Stores a new email message in the queue.
       *
       * @param mixed $to      email or array of emails as recipients
       * @param string $subject   Email's subject
       * @param array $data    associative array of variables to be passed to the email template
       *
       * @return bool
       */
  public static function enqueue($to, string $subject, array $data)
     {
         $queue = new EmailQueueModel();

         return $queue->enqueue($to, $subject, $data);
     }

  public static function process($limit = 10)
     {

       $queue = new EmailQueueModel();
       $emails = $queue->getBatch(0,$limit);

       if (! $query->num_rows()) {
            return true;
        }

       helper('setting');

       $totalSteps = count($emails);
       $currStep   = 0;

       if(is_cli()){
         CLI::write('Emails to send: '.$totalSteps, 'green');
      }

       $email = \Config\Services::email();

       $config['protocol'] = setting('Email.protocol');

       if(setting('Email.protocol') == 'sendmail'){
         $config['mailpath'] = '/usr/sbin/sendmail';
       }

       $config['wordWrap'] = true;
       $config['mailType'] = 'html';

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

             if(is_cli()){

               CLI::write('Cold not send email to: '.$e['email'], 'light_red');
               CLI::newLine();

           }

             $update_data = array(
               'attempts'=>$e['attempts']+1,
             );

           }else{

             $update_data = array(
               'sent_at'=>new Time('now'),
               'sent'=>1,
               'attempts'=>$e['attempts']+1,
             );
         }

         $queue->update($e['id'],$update_data);

          if(is_cli()){
            CLI::showProgress($currStep++, $totalSteps);
          }

       }
     }
}
