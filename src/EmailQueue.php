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
  public static function enqueue($to, string $subject, array $attachs, array $data)
     {
          $validation =  \Config\Services::validation();

          if($validation->check($to,'required|valid_email') && $validation->check($subject,'required')){

               $queue = new EmailQueueModel();

               return $queue->enqueue($to, $subject, $attachs, $data);

         }else{

           return redirect()->back()->with('error', $validation->getErrors());

         }
     }

     /**
          * Process queue.
          *
          * @param int $limit      numer of email to process
          *
          * @return bool
          */
  public static function process($limit = 10)
     {

       $queue = new EmailQueueModel();
       $emails = $queue->getBatch(0,$limit);

       // If the query returned no rows, the queue is empty, so it has been
      // processed successfully.
       if (count($emails) < 1) {
            return true;
        }

       helper('setting');

       $totalSteps = count($emails);
       $currStep   = 0;
       $success = true;

       if(is_cli()){
         CLI::write('Emails to send: '.$totalSteps, 'green');
      }

       $email = \Config\Services::email();

       $config['protocol'] = setting('Email.protocol');

       if(setting('Email.protocol') == 'sendmail'){
         $config['mailpath'] = '/usr/sbin/sendmail';
       }

       $config['userAgent'] = 'L2JPREMIUM MAIL';
       $config['wordWrap'] = true;
       $config['mailType'] = 'html';
       $config['validate'] = true;
       $config['newline'] = "\r\n";
       $config['CRLF'] = "\r\n";
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

           foreach(explode(",", $e['attachs']) as $a){
              $email->attach($a);
           }

           if(is_cli()){
            CLI::showProgress($currStep++, $totalSteps);
        }

           if (!$email->send(false)){

             if(is_cli()){

              CLI::write($email->printDebugger(['headers']));

               CLI::write('Could not send email to: '.$e['email'], 'light_red');
               CLI::newLine();

           }
        
             $updateData = array(
               'attempts'=>$e['attempts']+1,
             );

           }else{

             $updateData = array(
               'sent_at'=>new Time('now'),
               'sent'=>1,
               'attempts'=>$e['attempts']+1,
             );

            $success = false;
         }

         $queue->update($e['id'],$updateData);


       }

       //CLI::showProgress(false);

       return $success;

     }
}
